<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;

/**
 * Meeting Controller
 *
 * @Route("/meetings/{id}")
 */
class MeetingRegistrationController
{
    /**
     * Register
     *
     */
    public function registerUserMeeting(
        Meeting $meeting,
        User $user,
        EntityManagerInterface $em,
        MeetingRepository $meetingRepository,
        Request $request,
        UserRepository $userRepository
    ): Response
    {
        // Check user already exists
        $user = $userRepository->findBy(array('id' => $request->get('user_id')));
        if (!$user) {
            throw new HttpException(400, 'Userid invalid.');
        }

        $meeting = $meetingRepository->find(array('id' => $request->get('meeting_id')));
        if (!$meeting) {
            throw new HttpException(400, 'Meetingid invalid.');
        }

        $user->setMeeting($meeting);
        $meeting->setUser($user);

        $em->persist($user);
        $em->persist($meeting);
        $em->flush();
        return new Response('', Response::HTTP_NO_CONTENT, []);
    }

    /**
     * Register
     *
     */
    public function unregisterUserMeeting(
        EntityManagerInterface $em,
        MeetingRepository $meetingRepository,
        Request $request,
        UserRepository $userRepository
    ): Response
    {
        // Check user already exists
        $user = $userRepository->findBy(array('id' => $request->get('user_id')));
        if (!$user) {
            throw new HttpException(400, 'Userid invalid.');
        }

        $meeting = $meetingRepository->find(array('id' => $request->get('meeting_id')));
        if (!$meeting) {
            throw new HttpException(400, 'Meetingid invalid.');
        }

        $user->removeMeeting($meeting);
        //$meeting->removeUser($user);
        $em->remove($meeting);
        //$em->remove($user);
        $em->flush();
        return new Response('', Response::HTTP_NO_CONTENT, []);
    }
}
