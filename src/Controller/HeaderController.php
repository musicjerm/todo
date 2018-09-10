<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HeaderController extends Controller
{
    public function renderAction(): Response
    {
        return new Response();
    }
}