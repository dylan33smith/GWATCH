<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Form\DataUploadType;
use App\Repository\UserRepository;
use App\Service\ModuleCreationService;
use App\Service\CsvValidationService;
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
            $currentUser = $this->getUser();
            
            try {
                // Validate CSV files before creating module
                $csvValidationService = new CsvValidationService();
                $validationErrors = $csvValidationService->validateAllFiles(
                    $data['chrFile'],
                    $data['chrsuppFile'],
                    $data['colFile'],
                    $data['indFile'],
                    $data['rPvalFile'],
                    $data['rRatioFile'],
                    $data['vIndFile'],
                    $data['rowFile'],
                    $data['valFile']
                );
                
                if (!empty($validationErrors)) {
                    foreach ($validationErrors as $error) {
                        $this->addFlash('upload_error', $error);
                    }
                    return $this->render('upload/upload.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                
                // Create module using the service
                $moduleTracking = $moduleCreationService->createModule(
                    $data['moduleName'],
                    $data['description'],
                    $data['makePublic'] ?? false,
                    $currentUser,
                    $data['chrFile'],
                    $data['chrsuppFile'],
                    $data['colFile'],
                    $data['indFile'],
                    $data['rPvalFile'],
                    $data['rRatioFile'],
                    $data['vIndFile'],
                    $data['rowFile'],
                    $data['valFile']
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
