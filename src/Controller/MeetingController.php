<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meeting;
use App\Event\MeetingRegisteredEvent;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Meeting Controller
 *
 * @Route("/meetings", name="meetins_")
 */
class MeetingController
{

    /**
     * Create Meeting.
     *
     * @Route("/", name="post", methods="POST")
     *
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @param ValidatorInterface $validator
     * @param AdapterInterface $cache
     * @param UrlGeneratorInterface $router
     *
     */
    public function postMeeting(
        AdapterInterface $cache,
        ConstraintViolationListInterface $validationErrors,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Meeting $meeting,
        Request $request,
        UrlGeneratorInterface $router
    ): Response {

        if (count($validationErrors) > 0) {
            return new Response(array('errors' => $validationErrors), Response::HTTP_BAD_REQUEST);
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
        return new Response($meeting, Response::HTTP_CREATED, []);
    }

    /**
     * Lists all Meetings.
     * @Route("/", name="get_all", methods={"GET"})
     */
    public function getMeetings(CacheInterface $redisCache, MeetingRepository $meetingRepository, SerializerInterface $serializer)    {
        // add pagination on data using ParamFetcherInterface

        $meetings = $redisCache->get('meetings', function (ItemInterface $item) use ($meetingRepository) {
            return $meetingRepository->findAll();
        });


        $content = $serializer->serialize($meetings, 'json', ['groups' => 'user']);


        return new JsonResponse($content, Response::HTTP_OK, [], true);
    }

    /**
     * Get Meeting.
     *
     * @Route("/{id<\d+>?1}", name="meeting_index")
     */
    public function getMeeting($id, MeetingRepository $meetingRepository): Response
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

        return new Response($meeting, Response::HTTP_OK);
    }

    /**
     * Update an Meeting.
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
