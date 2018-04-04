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

/**
 * Brand Controller
 *
 * @Route("/api/v1")
 */

class ArticleController extends Controller {

	/**
     * Create Article.
     * @FOSRest\Post("/article")
     *
     * @return array
     */
    public function postArticleAction(Request $request)
    {
        $article = new Article();
        $article->setFromJson($request->getContent());
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return View::create($article, Response::HTTP_CREATED , []);
    }

    /**
     * Lists all Articles.
     * @FOSRest\Get("/articles")
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
     * Update an Article.
     * @FOSRest\Put(path = "/article/{id}")
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
     * Delete an Article.
     *
     * @FOSRest\Delete(path = "/article/{id}")
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

    /**
     * @Route("/test")
     */
    public function test() {
        return $this->render('base.html.twig');
    }
}
