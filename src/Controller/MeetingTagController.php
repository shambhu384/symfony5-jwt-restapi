<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Tag;
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
/**
 * Meeting Controller
 */
class MeetingTagController
{
    /**
     * @Route("/tags", name="meeting_tag", methods={"GET"})
     */
    public function index()
    {
        return new Response(['page' => 1], Response::HTTP_CREATED, []);
    }
}
