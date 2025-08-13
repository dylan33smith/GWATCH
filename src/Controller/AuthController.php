<?php

namespace App\Controller;

use App\Entity\Gwatch\User;
use App\Form\LoginType;
use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    /**
     * Handle user login form submission
     * 
     * @param Request $request The HTTP request
     * @param UserRepository $userRepository Repository for user operations
     * @param SessionInterface $session User session for authentication
     * @return Response Rendered login page or redirect
     */
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {
        // Check if user is already logged in
        if ($session->has('user_id')) {
            return $this->redirectToRoute('gwatch_home');
        }

        // Clear any existing flash messages to prevent old messages from showing
        // This ensures only current user's messages are displayed and prevents
        // messages from previous user sessions from appearing
        $session->getFlashBag()->clear();

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $username = $data['username'];
            $password = $data['password'];

            $user = $userRepository->findByUsername($username);

            if ($user && password_verify($password, $user->getPassword())) {
                // Set session data
                $session->set('user_id', $user->getId());
                $session->set('username', $user->getUsername());
                $session->set('user_role', $user->getRole());
                
                $this->addFlash('success', 'Login successful!');
                return $this->redirectToRoute('gwatch_home');
            } else {
                // Log failed login attempt for security monitoring
                error_log('Failed login attempt for username: ' . $username);
                $this->addFlash('error', 'Invalid username or password.');
            }
        }

        return $this->render('auth/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Handle user registration form submission
     * 
     * @param Request $request The HTTP request
     * @param EntityManagerInterface $entityManager Entity manager for database operations
     * @param UserRepository $userRepository Repository for user operations
     * @return Response Rendered registration page or redirect
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        SessionInterface $session
    ): Response {
        // Clear any existing flash messages to prevent old messages from showing
        // This ensures only current user's messages are displayed and prevents
        // messages from previous user sessions from appearing
        $session->getFlashBag()->clear();
        
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if username already exists
            if ($userRepository->findByUsername($user->getUsername())) {
                $this->addFlash('error', 'Username already exists.');
                return $this->render('auth/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Check if email already exists
            if ($userRepository->findByEmail($user->getMail())) {
                $this->addFlash('error', 'Email already registered.');
                return $this->render('auth/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Hash the password
            $hashedPassword = password_hash($user->getPassword(), PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);

            // Set default role
            $user->setRole('ROLE_USER');

            // Save the user
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Account created successfully! You can now login.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        // Add logout message before clearing session
        $this->addFlash('success', 'You have been logged out.');
        
        // Clear session data
        $session->clear();
        
        return $this->redirectToRoute('gwatch_home');
    }

    #[Route('/logout/confirm', name: 'app_logout_confirm')]
    public function logoutConfirm(SessionInterface $session): Response
    {
        // Check if user is logged in
        if (!$session->has('user_id')) {
            return $this->redirectToRoute('gwatch_home');
        }

        return $this->render('auth/logout_confirm.html.twig', [
            'username' => $session->get('username'),
        ]);
    }
}
