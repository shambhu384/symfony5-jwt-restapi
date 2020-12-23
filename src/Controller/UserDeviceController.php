<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\User;
use App\Event\MeetingRegisteredEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Context\Context;

/**
 * User devices
 *
 * @Route("/users")
 */

class UserDeviceController
{
    /**
     * @Route("/devices", name="user_device", methods={"GET"})
     * @OA\Tag(name="Miscellaneous")
     *
     * @return Response
     */
    public function device()
    {
        return new Response(['page' => 1], Response::HTTP_CREATED, []);
    }
}
