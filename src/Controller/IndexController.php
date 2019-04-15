<?php

namespace App\Controller;

use App\Service\Calculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController'
        ]);
    }

    /**
     * @Route("/calculate", name="calculate")
     * @throws \Exception
     */
    public function calculate(Request $request)
    {
        $expr = json_decode($request->getContent());

        try {
            $calc = new Calculator($expr->data);
            $result = $calc->calculate();
        } catch (Throwable $e) {
            $result = Calculator::ERROR;
        }

        return $this->json(['result' => $result]);
    }
}
