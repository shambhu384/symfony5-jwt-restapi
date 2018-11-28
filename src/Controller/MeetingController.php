<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use App\Entity\Meeting;
use App\Message\MeetingMessage;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\MeetingRegisteredEvent;
use App\MeetingEvents;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use App\Services\Interfaces\MeetingInterface;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Version;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Request\ParamConverter\MeetingConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Swagger\Annotations as SWG;


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
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Meetings of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="The field used to order Meetings"
     * )
     * @SWG\Tag(name="Meetings")
     */
    public function postMeeting(
        Meeting $meeting,
        Request $request,
        EventDispatcherInterface $dispatcher,
        ConstraintViolationListInterface $validationErrors,
        AdapterInterface $cache,
        UrlGeneratorInterface $router
    ): View {

        if (count($validationErrors) > 0) {
            return View::create(array('errors' => $validationErrors), Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($meeting);
        $em->flush();

        $meetingEvent = new MeetingRegisteredEvent($meeting);
        $dispatcher->dispatch(MeetingEvents::MEETING_REGISTERED, $meetingEvent);

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
        return View::create($response, Response::HTTP_CREATED, []);
    }

    /**
     * Lists all Meetings.
     * @FOSRest\Get("/meetings")
     *
     * @QueryParam(name="search", requirements="[a-z]+", description="search", allowBlank=false)
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @QueryParam(name="limit", requirements="\d+", default="5", description="How many notes to return.")
     * @QueryParam(name="sort", requirements="(asc|desc)", allowBlank=false, default="desc", description="Sort direction")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Meetings of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="The field used to order Meetings"
     * )
     * @SWG\Tag(name="Meetings")
     *
     * @return View
     */
    public function getMeetings(MeetingInterface $meetingService, ParamFetcherInterface $paramFetcher, AdapterInterface $cache, MessageBusInterface $bus): JsonResponse
    {
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
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Meetings of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="The field used to order Meetings"
     * )
     * @SWG\Tag(name="Meetings")
     *
     *
     * @return View
     */
    public function getMeeting($id): View
    {
        $repository = $this->getDoctrine()->getRepository(Meeting::class);

        // query for a single Product by its primary key (usually "id")
        $meeting = $repository->find($id);
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

        return View::create($response, Response::HTTP_OK, []);
    }

    /**
     * Update an Meeting.
     * @FOSRest\Put(path = "/meetings/{id}")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Meetings of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="The field used to order Meetings"
     * )
     * @SWG\Tag(name="Meetings")
     * @return View
     */
    public function putMeeting($id, Request $request): View
    {
        $em = $this->getDoctrine()->getManager();
        $meeting = $em->getRepository(Meeting::class)->find($id);
        if (!$meeting) {
            throw new HttpException(404, 'Meeting not found');
        }
        $postdata = json_decode($request->getContent());
        $meeting->setName($postdata->name);
        $meeting->setDescription($postdata->description);
        $meeting->setDateTime(new \DateTime($postdata->date));
        $em->persist($meeting);
        $em->flush();
        return View::create($meeting, Response::HTTP_OK, []);
    }

    /**
     * Delete an Meeting.
     *
     * @FOSRest\Delete(path = "/meetings")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Meetings of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="The field used to order Meetings"
     * )
     * @SWG\Tag(name="Meetings")
     *
     * @return View
     */
    public function deleteMeeting(Request $request): View
    {
        $em = $this->getDoctrine()->getManager();

        $meeting = $em->getRepository(Meeting::class)->find($request->get('meeting_id'));
        if (!$meeting) {
            throw new HttpException(404, 'Meeting not found');
        }
        $em->remove($meeting);
        $em->flush();
        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
