<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class MeetingRegistrationController
{
    /**
     * Register
     *
     * @Route("/meetings/{id}/register/{user}", name="meeting_user_register", methods={"POST"})
     * @OA\Tag(name="Meetings")
     */
    public function registerUserMeeting(
        Meeting $id,
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
     * Unregister
     *
     * @Route("/meetings/{id}/unregister/{user}", name="meeting_user_unregister", methods={"POST"})
     * @OA\Tag(name="Meetings")
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
