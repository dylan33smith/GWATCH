<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/hello/{name}', name: 'hello')]
    public function helloAction(string $name): Response
    {
        return $this->render('hello.html.twig', [
            # name is the Twig variable name
            # $name is the php variable being passed in
            'name' => $name,
        ]);
    }
}