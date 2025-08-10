<?php

namespace App\Controller;

use App\Service\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class GwatchController extends AbstractController
{
    private $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    #[Route('/', name: 'gwatch_home')]
    public function home(): Response
    {
        return $this->render('gwatch/home.html.twig');
    }

    #[Route('/description', name: 'gwatch_description')]
    public function description(): Response
    {
        return $this->render('gwatch/description.html.twig');
    }

    #[Route('/features', name: 'gwatch_features')]
    public function features(): Response
    {
        return $this->render('gwatch/features.html.twig');
    }

    #[Route('/tutorial', name: 'gwatch_tutorial')]
    public function tutorial(): Response
    {
        return $this->render('gwatch/tutorial.html.twig');
    }

    #[Route('/datasets', name: 'gwatch_datasets')]
    public function datasets(): Response
    {
        return $this->render('gwatch/datasets.html.twig');
    }


} 