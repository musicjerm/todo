<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('@JermBundle/Base/index.html.twig');
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profileAction()
    {
        return $this->render('@JermBundle/Base/index.html.twig');
    }
}