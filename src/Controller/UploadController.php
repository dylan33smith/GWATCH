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
                $this->addFlash('info', 'Starting module creation...');
                
                // Log what files are being processed
                if ($data['densityFile'] !== null) {
                    $this->addFlash('info', 'Density file detected: ' . $data['densityFile']->getClientOriginalName() . ' (Size: ' . $data['densityFile']->getSize() . ' bytes)');
                }
                if ($data['radiusIndFile'] !== null) {
                    $this->addFlash('info', 'Radius index file detected: ' . $data['radiusIndFile']->getClientOriginalName() . ' (Size: ' . $data['radiusIndFile']->getSize() . ' bytes)');
                }
                
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
                    $data['valFile'],
                    $data['densityFile'] ?? null,
                    $data['radiusIndFile'] ?? null
                );
                
                $this->addFlash('upload_success', 'Module "' . $data['moduleName'] . '" created successfully! Module ID: Module_' . $moduleTracking->getId());
                
                return $this->redirectToRoute('app_upload');
                
            } catch (\Exception $e) {
                // Log technical error for debugging
                error_log('Module creation error: ' . $e->getMessage());
                
                // Show the real error message for debugging
                $errorMessage = 'Module creation failed: ' . $e->getMessage();
                
                // Add file and line information for better debugging
                if ($e->getFile() && $e->getLine()) {
                    $errorMessage .= ' (File: ' . basename($e->getFile()) . ':' . $e->getLine() . ')';
                }
                
                // Add stack trace for detailed debugging
                $errorMessage .= ' Stack trace: ' . $e->getTraceAsString();
                
                $this->addFlash('error', $errorMessage);
            }
        }

        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
            'username' => $session->get('username'),
        ]);
    }
}
