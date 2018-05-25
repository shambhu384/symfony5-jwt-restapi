<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use App\Entity\Meeting;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\MeetingRegisteredEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;


/**
 * Meeting Controller
 *
 * @Route("/api/v1")
 */

class MeetingTagController extends Controller
{
    /**
     * @Route("/tag", name="meeting_tag")
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     */
    public function index(ParamFetcherInterface $paramFetcher)
    {
        return View::create(['page' => $paramFetcher->get('page')], Response::HTTP_CREATED , []);
    }
}
