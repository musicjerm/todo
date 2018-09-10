<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class HeaderController extends Controller
{
    public function renderAction(UserInterface $user): Response
    {
        return new Response();
    }
}