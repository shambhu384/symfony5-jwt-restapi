<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Service\RuleManager;
use App\Service\IsNumericRule;
use App\Service\GreaterThanRule;

class DefaultController extends Controller
{
    /**
     * @Route("/default", name="default")
     */
    public function index()
    {
        $data = [2000,4000,8000,10000, 'abc', 20000, 40000, 'xyz'];

        $ruleManager = $this->get('app.rule_manager');

        $data = $ruleManager->applyRules($data);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'data' => $data
        ]);
    }
}
