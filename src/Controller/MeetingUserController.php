<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Services\Interfaces\MeetingInterface;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
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
 *
 * @Version("v1")
 */
class MeetingUserController extends AbstractController
{
    /**
     * @FOSRest\Post("/users")
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function postUser(
        User $user,
        Request $request,
        UserRepository $userRepository,
        ConstraintViolationListInterface $validationErrors,
        EntityManagerInterface $em
    ): View
    {
        if (count($validationErrors) > 0) {
            return View::create(array('errors' => $validationErrors), Response::HTTP_BAD_REQUEST);
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

        return View::create($user, Response::HTTP_CREATED, ['context' => ['user']]);
    }

    /**
     * @FOSRest\Get("/users")
     *
     * @QueryParam(name="search", requirements="[a-z]+", description="search", allowBlank=false)
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     * @QueryParam(name="limit", requirements="\d+", default="5", description="How many notes to return.")
     * @QueryParam(name="sort", requirements="(asc|desc)", allowBlank=false, default="desc", description="Sort direction")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the Meetings of an user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="The field used to order Meetings"
     * )
     * @SWG\Tag(name="User")
     */
    public function getUsers(ParamFetcherInterface $paramFetcher, AdapterInterface $cache, EntityManagerInterface $em, UserRepository $userRepository): View
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

        return View::create(array(
            "metadata" => array("limit" => (int)$limit, "start"=> $page),
            'collections' => $response
        ), Response::HTTP_OK, []);
    }

    /**
     * Get Meeting.
     * @FOSRest\Get(path = "/users/{id}", name="user_index")
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
     * @SWG\Tag(name="User")
     *
     *
     * @return View
     */
    public function getApiUser($id, UserRepository $userRepository): View
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

        return View::create($response, Response::HTTP_OK, []);
    }


}
