<?php

declare(strict_types=1);


namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Version;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;


/**
 * User devices
 *
 * @Route("/users")
 * @Version("v1")
 */

class UserDeviceController extends AbstractController
{
    /**
     * @Route("/devices", name="user_device")
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page of the overview.")
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
     * @SWG\Tag(name="Device")
     */
    public function index(ParamFetcherInterface $paramFetcher)
    {
        return View::create(['page' => $paramFetcher->get('page')], Response::HTTP_CREATED, []);
    }
}
