<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use App\Entity\Article;
use App\Entity\User;

/**
 * Meeting Controller
 *
 * @Route("/api/v1")
 */

class MeetingController extends Controller {

	/**
     * Create Meeting.
     * @FOSRest\Post("/meeting")
     *
     * @return array
     */
    public function postArticleAction(Request $request)
    {
        $postdata = json_decode($request->getContent());
        $article = new Article();
        $article->setName($postdata->name);
        $article->setDescription($postdata->description);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($postdata->userid);
        $article->setUser($user);
        $em->persist($article);
        $em->flush();
        return View::create($article, Response::HTTP_CREATED , []);
    }

    /**
     * Lists all Meetings.
     * @FOSRest\Get("/meeting")
     *
     * @return array
	 */
	public function getArticleAction()
	{
		$repository = $this->getDoctrine()->getRepository(Article::class);

		// query for a single Product by its primary key (usually "id")
		$article = $repository->findall();

		return View::create($article, Response::HTTP_OK , []);
    }

    /**
     * Update an Meeting.
     * @FOSRest\Put(path = "/meeting/{id}")
     *
     * @return array
	 */
    public function putArticleAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);
        $article->setFromJson($request->getContent());
        $em->persist($article);
        $em->flush();
		return View::create($article, Response::HTTP_OK , []);
    }

    /**
     * Delete an Meeting.
     *
     * @FOSRest\Delete(path = "/meeting/{id}")
     *
     * @return array
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);
        $em->remove($article);
        $em->flush();
        return View::create(null, Response::HTTP_NO_CONTENT);
    }

}
