<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Swagger\Annotations as SWG;
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
 * @Version("v1")
 */
class MeetingRegistrationController extends AbstractController
{
    /**
     * Register
     * @FOSRest\Post("/user/{user}")
     *
     * @return View
     */
    public function registerUserMeeting(
        Meeting $meeting,
        User $user,
        EntityManagerInterface $em,
        MeetingRepository $meetingRepository,
        Request $request,
        UserRepository $userRepository
    ): View
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
        return View::create('', Response::HTTP_NO_CONTENT, []);
    }

    /**
     * Register
     * @FOSRest\DELETE("/unregistration")
     *
     * @return View
     */
    public function unregisterUserMeeting(
        EntityManagerInterface $em,
        MeetingRepository $meetingRepository,
        Request $request,
        UserRepository $userRepository
    ): View
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
        return View::create('', Response::HTTP_NO_CONTENT, []);
    }
}
