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
    public function home(SessionInterface $session): Response
    {
        // Clear any old flash messages when accessing home page
        // This prevents messages from previous user sessions from appearing
        if (!$session->has('user_id')) {
            $session->getFlashBag()->clear();
        }
        
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

    /**
     * Display datasets page with user-specific and public modules
     * 
     * @param ModuleTrackingRepository $moduleTrackingRepository Repository for module operations
     * @param UserRepository $userRepository Repository for user operations
     * @param SessionInterface $session User session for authentication
     * @return Response Rendered datasets page
     */
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
                $userModules = $this->fetchUserModules($moduleTrackingRepository, $currentUser);
            }
        }
        
        // Fetch all public modules, excluding those owned by the current user
        $publicModules = $this->fetchPublicModules($moduleTrackingRepository, $isLoggedIn, $currentUser);

        return $this->render('gwatch/datasets.html.twig', [
            'isLoggedIn' => $isLoggedIn,
            'currentUser' => $currentUser,
            'userModules' => $userModules,
            'publicModules' => $publicModules,
        ]);
    }

    /**
     * Fetch modules owned by the current user
     */
    private function fetchUserModules(ModuleTrackingRepository $moduleTrackingRepository, User $currentUser): array
    {
        return $moduleTrackingRepository->createQueryBuilder('m')
            ->where('m.owner = :owner')
            ->setParameter('owner', $currentUser)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch public modules, excluding those owned by the current user
     */
    private function fetchPublicModules(ModuleTrackingRepository $moduleTrackingRepository, bool $isLoggedIn, ?User $currentUser): array
    {
        $publicModulesQuery = $moduleTrackingRepository->createQueryBuilder('m')
            ->where('m.public = :public')
            ->setParameter('public', true);
            
        if ($isLoggedIn && $currentUser) {
            $publicModulesQuery->andWhere('m.owner != :currentUser')
                ->setParameter('currentUser', $currentUser);
        }
        
        return $publicModulesQuery
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 