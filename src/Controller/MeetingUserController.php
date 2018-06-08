<?php

declare(strict_types=1);


namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MeetingUserController extends Controller
{
    /**
     * @FOSRest\Post("/api/v1/meeting/user")
     */
    public function postUserAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        // Check user already exists
        $user = $userManager->findUserByUsername($request->get('username'));

        if($user) {
            // check duplicate email
            if($user->getemail() == $request->get('email'))
                throw new HttpException(400, 'email already exists.');

            throw new HttpException(400, 'username already exists.');
        }

        $user = $userManager->createUser();
        $user->setUsername($request->get('username'));
        $user->setFullName($request->get('fullname'));
        $user->setEmail($request->get('email'));
        $user->setPlainPassword($request->get('password'));
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        // Save user
        $userManager->updateUser($user);

        // Add UserNormalizer to return normalize entity
        $response  =array(
            'id' => $user->getId(),
            'fullname' => $user->getFullName(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreated()
        );
        return View::create($response, Response::HTTP_CREATED , []);
    }
}
