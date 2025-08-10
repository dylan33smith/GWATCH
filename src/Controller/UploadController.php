<?php

namespace App\Controller;

use App\Entity\Gwatch\ModuleTracking;
use App\Entity\Gwatch\User;
use App\Form\DataUploadType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    #[Route('/upload', name: 'app_upload')]
    public function upload(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
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
            
            try {
                // Fetch the current user entity
                $currentUser = $userRepository->find($session->get('user_id'));
                if (!$currentUser) {
                    throw new \Exception('User not found');
                }
                
                // Generate a unique module ID using module name and timestamp
                $timestamp = date('YmdHis');
                $moduleId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $data['moduleName'])) . '_' . $timestamp;
                
                // Create new module tracking entry
                $moduleTracking = new ModuleTracking();
                $moduleTracking->setModuleId($moduleId);
                $moduleTracking->setName($data['moduleName']);
                $moduleTracking->setOwner($currentUser);
                $moduleTracking->setPublic($data['makePublic'] ?? false);
                $moduleTracking->setDescription($data['description']);
                
                // Persist to database
                $entityManager->persist($moduleTracking);
                $entityManager->flush();
                
                $this->addFlash('upload_success', 'Module "' . $data['moduleName'] . '" created successfully! Module ID: ' . $moduleId);
                
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
