<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meeting;
use App\Event\MeetingRegisteredEvent;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\RestBundle\Context\Context;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Meeting Controller
 *
 * @Version("v1")
 */
class MeetingController extends FOSRestController
{

    /**
     * Create Meeting.
     *
     * @FOSRest\Post("/meetings")
     *
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @param ValidatorInterface $validator
     * @param AdapterInterface $cache
     * @param UrlGeneratorInterface $router
     *
     * @ParamConverter("meeting", converter="fos_rest.request_body")
     */
    public function postMeeting(
        AdapterInterface $cache,
        ConstraintViolationListInterface $validationErrors,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Meeting $meeting,
        Request $request,
        UrlGeneratorInterface $router
    ): View {

        if (count($validationErrors) > 0) {
            return View::create(array('errors' => $validationErrors), Response::HTTP_BAD_REQUEST);
        }

        $em->persist($meeting);
        $em->flush();

        $dispatcher->dispatch(new MeetingRegisteredEvent($meeting));

        $response = array(
            'id' => $meeting->getId(),
            'name' => $meeting->getName(),
            'description' => $meeting->getDescription(),
            'date' => $meeting->getDateTime(),
            'url' => $router->generate(
                'api_meeting_index',
                array('id' => $meeting->getId(), 'version' => 'v1')
            )
        );
        return View::create($meeting, Response::HTTP_CREATED, []);
    }

    /**
     * Lists all Meetings.
     * @FOSRest\Get("/meetings")
     * @QueryParam(name="search", requirements="[a-z]+", description="search", allowBlank=false)
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @QueryParam(name="limit", requirements="\d+", default="5", description="How many notes to return.")
     * @QueryParam(name="sort", requirements="(asc|desc)", allowBlank=false, default="desc", description="Sort direction")
     *
     * @return View
     */
    public function getMeetings(
        AdapterInterface $cache,
        MeetingRepository $meetingRepository,
        MessageBusInterface $bus,
        ParamFetcherInterface $paramFetcher
    )    {
        $repository = $this->getDoctrine()->getRepository(Meeting::class);
        // add pagination on data using ParamFetcherInterface
        $limit = $paramFetcher->get('limit');
        $page = $limit * ($paramFetcher->get('page') - 1);
        $meetings = $repository->findBy(array(), array('id' => $paramFetcher->get('sort')), $limit, $page);

        // Move this in Meeting normalizer
        $response = array();
        foreach ($meetings as $meeting) {
            // find users
            $users = [];
            foreach ($meeting->getUsers() as $user) {
                $users[] = array(
                    'id' => $user->getId(),
                    'fullname' => $user->getFullName(),
                    'email' => $user->getEmail()
                );
            }

            $response[] = array(
                'id' => $meeting->getId(),
                'name' => $meeting->getName(),
                'description' => $meeting->getDescription(),
                'date' => $meeting->getDateTime(),
                'organiser' => $meeting->getOrganiser(),
                'users' => $users
            );
        }

        // send notification
        //$bus->dispatch(new MeetingMessage());

        return new JsonResponse(array(
            "_links" => array(
                "next" => sprintf('/meetings?limit=%d&page=%d', $limit, $paramFetcher->get('page')  + 1),
                "prev" => sprintf('/meetings?limit=%d&page=%d', $limit, $paramFetcher->get('page')  - 1),
            ),
            'limit' => $limit,
            'results' => $response,
            'size' => (int) $limit,
            'start' => $page
        ), Response::HTTP_OK, []);
    }

    /**
     * Get Meeting.
     * @FOSRest\Get(path = "/meetings/{id}", name="meeting_index")
     * @return View
     */
    public function getMeeting($id, MeetingRepository $meetingRepository): View
    {
        // query for a single Product by its primary key (usually "id")
        $meeting = $meetingRepository->find($id);
        if (!$meeting) {
            throw new HttpException(404, 'Meeting not found');
        }
        // Move this in Meeting normalizer
        $response = array(
            'id' => $meeting->getId(),
            'name' => $meeting->getName(),
            'description' => $meeting->getDescription(),
            'date' => $meeting->getDateTime(),
            'users' => [],
            'tags' => []
        );
        $users = $meeting->getUsers();
        if ($users) {
            foreach ($users as $user) {
                $response['users'][] = array(
                    'id' => $user->getId(),
                    'fullname' => $user->getFullName(),
                    'email' => $user->getEmail(),
                );
            }
        }

        $tags = $meeting->getTags();
        if ($tags) {
            foreach ($tags as $tag) {
                $response['tags'][] = $tag->getName();
            }
        }

        return View::create($meeting, Response::HTTP_OK);
    }

    /**
     * Update an Meeting.
     * @FOSRest\Put(path = "/meetings/{id}")
     *
     * @return View
     */
    public function putMeeting($id, Request $request, MeetingRepository $meetingRepository, EntityManagerInterface $em): View
    {
        $meeting = $meetingRepository->find($id);
        if (!$meeting) {
            throw new HttpException(404, 'Meeting not found');
        }
        $postdata = json_decode($request->getContent());
        $meeting->setName($postdata->name);
        $meeting->setDescription($postdata->description);
        $meeting->setDateTime(new \DateTime($postdata->datetime));
        $em->persist($meeting);
        $em->flush();
        return View::create($meeting, Response::HTTP_OK, []);
    }

    /**
     * Delete an Meeting.
     *
     * @FOSRest\Delete(path = "/meetings/{id}")
     * @return View
     */
    public function deleteMeeting($id, Request $request, MeetingRepository $meetingRepository, EntityManagerInterface $em): View
    {
        $meeting = $meetingRepository->find($id);
        if (!$meeting) {
            throw new HttpException(404, 'Meeting not found');
        }
        $em->remove($meeting);
        $em->flush();
        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
