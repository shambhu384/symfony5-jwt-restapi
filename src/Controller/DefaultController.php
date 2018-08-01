<?php


declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\RuleManager;
use App\Service\IsNumericRule;
use App\Service\GreaterThanRule;
use Psr\Log\LoggerInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="Landingpage")
     */
    public function index(LoggerInterface $logger)
    {
        $logger->info('I just got the logger');
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
