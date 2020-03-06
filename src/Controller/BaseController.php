<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/base", name="base")
     */
    public function index()
    {
        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }


    /**
     * @Route("/qui_sommes_nous", name="aboutus")
     */
    public function aboutUs(){
        return $this->render('about/index.html.twig',[]);
    }
}
