<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Form\DataUploadType;
use App\Repository\UserRepository;
use App\Service\ModuleCreationService;
use App\Service\CsvValidationService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    private CsvValidationService $csvValidationService;

    public function __construct(CsvValidationService $csvValidationService)
    {
        $this->csvValidationService = $csvValidationService;
    }

    /**
     * Handle data upload form submission and module creation
     * 
     * @param Request $request The HTTP request
     * @param SessionInterface $session User session for authentication
     * @param UserRepository $userRepository Repository for user operations
     * @param ModuleCreationService $moduleCreationService Service for creating modules
     * @return Response Rendered upload page or redirect
     */
    #[Route('/upload', name: 'app_upload')]
    public function upload(
        Request $request, 
        SessionInterface $session, 
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
            $currentUser = $userRepository->find($session->get('user_id'));
            
            if (!$currentUser) {
                $this->addFlash('error', 'User session expired. Please login again.');
                return $this->redirectToRoute('app_login');
            }
            
            try {
                // Validate CSV files before creating module
                $validationErrors = $this->csvValidationService->validateAllFiles(
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
                // Log technical error for debugging
                error_log('Module creation error: ' . $e->getMessage());
                $this->addFlash('error', 'An error occurred while creating the module. Please try again or contact support if the problem persists.');
            }
        }

        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
            'username' => $session->get('username'),
        ]);
    }
}
