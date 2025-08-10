<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Repository\ModuleTrackingRepository;
use App\Repository\UserRepository;
use App\Service\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function datasets(ModuleTrackingRepository $moduleTrackingRepository, UserRepository $userRepository, SessionInterface $session): Response
    {
        // Check if user is logged in
        $isLoggedIn = $session->has('user_id');
        $currentUser = null;
        $userModules = [];
        
        if ($isLoggedIn) {
            $currentUser = $userRepository->find($session->get('user_id'));
            if ($currentUser) {
                // Fetch modules owned by the current user
                $userModules = $moduleTrackingRepository->createQueryBuilder('m')
                    ->where('m.owner = :owner')
                    ->setParameter('owner', $currentUser)
                    ->orderBy('m.createdAt', 'DESC')
                    ->getQuery()
                    ->getResult();
            }
        }
        
        // Fetch all public modules, excluding those owned by the current user
        $publicModulesQuery = $moduleTrackingRepository->createQueryBuilder('m')
            ->where('m.public = :public')
            ->setParameter('public', true);
            
        if ($isLoggedIn && $currentUser) {
            $publicModulesQuery->andWhere('m.owner != :currentUser')
                ->setParameter('currentUser', $currentUser);
        }
        
        $publicModules = $publicModulesQuery
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('gwatch/datasets.html.twig', [
            'isLoggedIn' => $isLoggedIn,
            'currentUser' => $currentUser,
            'userModules' => $userModules,
            'publicModules' => $publicModules,
        ]);
    }
} 