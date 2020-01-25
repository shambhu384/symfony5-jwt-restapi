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

/**
 * Meeting user
 */
class MeetingUserController
{
    /**
     */
    public function postUser(
        User $user,
        Request $request,
        UserRepository $userRepository,
        ConstraintViolationListInterface $validationErrors,
        EntityManagerInterface $em
    ): Response
    {
        if (count($validationErrors) > 0) {
            return new Response(array('errors' => $validationErrors), Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        // Add UserNormalizer to return normalize entity
        $response  = array(
            'id' => $user->getId(),
            'fullname' => $user->getFullName(),
            'email' => $user->getEmail(),
            'created_at' => null
        );

        return new Response($user, Response::HTTP_CREATED, ['context' => ['user']]);
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
     * Get Meeting.
     * @return Response
     */
    public function getApiUser($id, UserRepository $userRepository): Response
    {
        // query for a single Product by its primary key (usually "id")
        $user = $userRepository->find($id);
        if (!$user) {
            throw new HttpException(404, 'User not found');
        }
        // Move this in Meeting normalizer
        $response = array(
            'id' => $user->getId(),
            'name' => $user->getEmail(),
        );

        return new Response($response, Response::HTTP_OK, []);
    }
}
