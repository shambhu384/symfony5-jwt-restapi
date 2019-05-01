<?php


declare(strict_types=1);


namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Tag;
use App\Entity\User;
use App\Event\MeetingRegisteredEvent;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Swagger\Annotations as SWG;
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
 *
 * @Version("v1")
 */
class MeetingTagController extends AbstractController
{
    /**
     * @Route("/tags", name="meeting_tag", methods={"GET"})
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
     */
    public function index(ParamFetcherInterface $paramFetcher)
    {
        return View::create(['page' => $paramFetcher->get('page')], Response::HTTP_CREATED, []);
    }
}
