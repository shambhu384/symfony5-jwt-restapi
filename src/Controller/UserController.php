<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Services\Interfaces\MeetingInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Context\Context;


/**
 * Meeting user
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @OA\RequestBody(
     *   description= "Provide company search parameter",
     *   required= true,
     *   @OA\JsonContent(
     *      type="object",
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="password", type="string")
     *    )
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Returns empty body with 201 status code"
     * )
     *
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     *
     * @OA\Tag(name="Meetings")
     * @Route("/users", name="users_post", methods={"POST"})
     */
    public function postUser(
        EntityManagerInterface $em,
        Request $request,
        SerializerInterface $serializer,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ): Response
    {
        // deserialize the json
        try {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (NotEncodableValueException $exception) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid Json');
        }

        $errors = $validator->validate($user);

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

        $em->persist($user);
        $em->flush();

        return new Response(null, Response::HTTP_CREATED);
    }

    /**
     */
    public function getUsers(AdapterInterface $cache, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        // add pagination on data using ParamFetcherInterface
        $limit = $paramFetcher->get('limit');
        $page = $limit * ($paramFetcher->get('page') - 1);
        $users = $userRepository->findAll(array(), array('id' => $paramFetcher->get('sort')), $limit, $page);
        // Move this in User normalizer
        $response = array();
        if (count($users) > 0) {
            foreach ($users as $user) {
                // find users
                $response[] = array(
                    'id' => $user->getId(),
                    'name' => $user->getFullName(),
                    'email' => $user->getEmail()
                );
            }
        }

        return new Response(array(
            "metadata" => array("limit" => (int)$limit, "start"=> $page),
            'collections' => $response
        ), Response::HTTP_OK, []);
    }

    /**
     * Get User
     *
     * @Route("/users/{id}", name="users_get", methods={"GET"})
     * @OA\Tag(name="Users")
     * @return Response
     */
    public function getApiUser($id, UserRepository $userRepository): View
    {
        // query for a single Product by its primary key (usually "id")
        $user = $userRepository->find($id);
        if (!$user) {
            throw new HttpException(404, 'User not found');
        }

        $context = new Context();
        $context->setVersion('1.0');
        $context->addGroup('user');

        $view = $this->view($user, 200);
        $view->setContext($context);

        return $this->handleView($view);
    }
}
