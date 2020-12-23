<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meeting;
use App\Event\MeetingRegisteredEvent;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Meeting Controller
 */
class MeetingController extends AbstractFOSRestController
{
    /**
     * Create Meeting.
     *
     * @Route("/meetings", name="post", methods="POST")
     *
     * @OA\RequestBody(
     *   description= "Provide company search parameter",
     *   required= true,
     *   @OA\JsonContent(
     *      type="object",
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="description", type="string"),
     *       @OA\Property(property="meetingAt", type="string")
     *    )
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Returns empty body with 201 status code"
     * )
     *
     * @OA\Tag(name="Meetings")
     * @Security(name="Bearer")
     *
     * @param AdapterInterface $cache
     * @param EventDispatcherInterface $dispatcher
     * @param ValidatorInterface $validator
     * @param AdapterInterface $cache
     * @param UrlGeneratorInterface $router
     *
     */
    public function postMeeting(
        AdapterInterface $cache,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Request $request,
        SerializerInterface $serializer,
        UrlGeneratorInterface $router,
        ValidatorInterface $validator
    ): Response {

        // deserialize the json
        try {
            $meeting = $serializer->deserialize($request->getContent(), Meeting::class, 'json');
        } catch (NotEncodableValueException $exception) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid Json');
        }

        $errors = $validator->validate($meeting);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $json = $serializer->serialize($errors, 'json', array_merge([
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ], []));

            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $meeting->setOrganiser($this->getUser()->getId());

        $em->persist($meeting);
        $em->flush();

        $dispatcher->dispatch(new MeetingRegisteredEvent($meeting));
        return new Response(null, Response::HTTP_CREATED);
    }

    /**
     * Lists all Meetings.
     * @Route("/meetings", name="get_all", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the meetings of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="order",
     *     in="query",
     *     description="The field used to order meetings",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Meetings")
     * @Security(name="Bearer")
  
     */
    public function getMeetings(CacheInterface $redisCache, MeetingRepository $meetingRepository, SerializerInterface $serializer)    {
        // add pagination on data using ParamFetcherInterface

        $meetings = $redisCache->get('meetings', function (ItemInterface $item) use ($meetingRepository) {
            return $meetingRepository->findAll();
        });

          $context = new Context();
    $context->setVersion('1.0');
        $context->addGroup('user');

        $view = $this->view($meetings, 200);
            $view->setContext($context);


        return $this->handleView($view);
    }

    /**
     * Get Meeting.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the meetings of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="order",
     *     in="query",
     *     description="The field used to order meetings",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Meetings")
     * @Security(name="Bearer")
     * @Route("/meetings/{id<\d+>?1}", name="meeting_index", methods={"GET"})
     */
    public function getMeeting($id, MeetingRepository $meetingRepository): Response
    {
        // query for a single Product by its primary key (usually "id")
        $meeting = $meetingRepository->find($id);
        if (!$meeting) {
            throw new HttpException(404, 'Meeting not found');
        }

        $context = new Context();
        $context->setVersion('1.0');
        $context->addGroup('user');

        $view = $this->view($meeting, 200);
        $view->setContext($context);

        // Move this in Meeting normalizer
        return $this->handleView($view);
    }

    /**
     * Update an Meeting.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the meetings of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Meeting::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="order",
     *     in="query",
     *     description="The field used to order meetings",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Meetings")
     * @Security(name="Bearer")
     *
     * @Route("/meetings/{id<\d+>?1}", name="meeting_put", methods={"PUT"})
     * @return View
     */
    public function putMeeting($id, Request $request, MeetingRepository $meetingRepository, EntityManagerInterface $em): View
    {
        $meeting = $meetingRepository->find($id);
        if (!$meeting) {
            throw new HttpException(404, 'Meeting not found');
        }
        $postdata = json_decode($request->getContent());
        $meeting->setDescription($postdata->description);
        $meeting->setDateTime(new \DateTime($postdata->datetime));
        $em->persist($meeting);
        $em->flush();
        return View::create($meeting, Response::HTTP_OK, []);
    }

    /**
     * Delete an Meeting.
     *
     * @OA\Parameter(
     *     name="order",
     *     in="query",
     *     description="The field used to order meetings",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Meetings")
     * @Security(name="Bearer")
     *
     * @Route("/meetings/{id<\d+>?1}", name="meeting_delete", methods={"DELETE"})
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
