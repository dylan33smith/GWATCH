<?php

namespace App\Controller;

use App\Form\DataUploadType;
use App\Service\DataUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    #[Route('/upload', name: 'app_upload')]
    public function upload(Request $request, SessionInterface $session, DataUploadService $uploadService): Response
    {
        // Check if user is logged in
        if (!$session->has('user_id')) {
            $this->addFlash('error', 'Please login to access the upload page.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(DataUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Prepare files array for the service
            $files = [
                'chr.csv' => $data['chrFile'],
                'chrsupp.csv' => $data['chrsuppFile'],
                'col.csv' => $data['colFile'],
                'ind.csv' => $data['indFile'],
                'r_pval.csv' => $data['rPvalFile'],
                'r_ratio.csv' => $data['rRatioFile'],
                'v_ind.csv' => $data['vIndFile'],
                'row.csv' => $data['rowFile'],
                'val.csv' => $data['valFile'],
            ];

            try {
                $result = $uploadService->processUpload(
                    $files,
                    $data['moduleName'],
                    $data['description'],
                    $data['makePublic'] ?? false,
                    $session->get('user_id')
                );

                if (isset($result['success'])) {
                    $this->addFlash('upload_success', 'Module "' . $data['moduleName'] . '" uploaded successfully! Module ID: ' . $result['moduleId']);
                    
                    if (!empty($result['warnings'])) {
                        foreach ($result['warnings'] as $warning) {
                            $this->addFlash('warning', $warning);
                        }
                    }
                    
                    return $this->redirectToRoute('app_upload');
                } else {
                    foreach ($result['errors'] as $error) {
                        $this->addFlash('error', $error);
                    }
                    if (!empty($result['warnings'])) {
                        foreach ($result['warnings'] as $warning) {
                            $this->addFlash('warning', $warning);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred during upload: ' . $e->getMessage());
            }
        }

        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
            'username' => $session->get('username'),
        ]);
    }
}
