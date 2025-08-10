<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Form\DataUploadType;
use App\Repository\UserRepository;
use App\Service\ModuleCreationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    #[Route('/upload', name: 'app_upload')]
    public function upload(
        Request $request, 
        SessionInterface $session, 
        EntityManagerInterface $entityManager, 
        UserRepository $userRepository,
        ModuleCreationService $moduleCreationService
    ): Response {
        // Check if user is logged in
        if (!$session->has('user_id')) {
            $this->addFlash('error', 'Please login to access the upload page.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(DataUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            try {
                // Fetch the current user entity
                $currentUser = $userRepository->find($session->get('user_id'));
                if (!$currentUser) {
                    throw new \Exception('User not found');
                }
                
                // Create module using the service
                $moduleTracking = $moduleCreationService->createModule(
                    $data['moduleName'],
                    $data['description'],
                    $data['makePublic'] ?? false,
                    $currentUser,
                    $data['chrFile'],
                    $data['chrsuppFile'] ?? null,
                    $data['colFile'] ?? null,
                    $data['indFile'] ?? null,
                    $data['rPvalFile'] ?? null,
                    $data['rRatioFile'] ?? null,
                    $data['vIndFile'] ?? null,
                    $data['rowFile'] ?? null,
                    $data['valFile'] ?? null
                );
                
                $this->addFlash('upload_success', 'Module "' . $data['moduleName'] . '" created successfully! Module ID: Module_' . $moduleTracking->getId());
                
                return $this->redirectToRoute('app_upload');
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while creating the module: ' . $e->getMessage());
            }
        }

        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
            'username' => $session->get('username'),
        ]);
    }
}
