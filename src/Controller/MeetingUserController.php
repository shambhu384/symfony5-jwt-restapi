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
/**
 * Meeting user
 *
 * @Version("v1")
 */
class MeetingUserController extends AbstractController
{
    /**
     * @FOSRest\Post("/users")
     */
    public function postUser(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository)
    {
        // Check user already exists
        $user = $userRepository->findBy(['email' => $request->get('username')]);

        if ($user) {
            // check duplicate email
            if ($user->getemail() == $request->get('email')) {
                throw new HttpException(400, 'email already exists.');
            }

            throw new HttpException(400, 'username already exists.');
        }

        $user = new User;
        $user->setUsername($request->get('username'));
        $user->setFullName($request->get('fullname'));
        $user->setEmail($request->get('email'));
        $user->setPlainPassword($request->get('password'));
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        // Save user
        $userManager->updateUser($user);

        // Add UserNormalizer to return normalize entity
        $response  = array(
            'id' => $user->getId(),
            'fullname' => $user->getFullName(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreated()
        );

        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('send@example.com')
        ->setTo('recipient@example.com')
        ->setBody(
            $this->renderView(
                'emails/registration.html.twig',
                array('name' => $user->getFullName())
            ),
            'text/html'
        );

        $mailer->send($message);

        return View::create($response, Response::HTTP_CREATED, []);
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
