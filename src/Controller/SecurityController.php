<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'translation_domain' => 'admin',
            'page_title' => 'ReserveEngine',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin_dashboard'),
            'username_label' => 'Your username',
            'password_label' => 'Your password',
            'sign_in_label' => 'Log in',
            'username_parameter' => '_username',
            'password_parameter' => '_password',
            'forgot_password_enabled' => false,
//            'forgot_password_path' => $this->generateUrl(''),
            'forgot_password_label' => 'Forgot your password?',
            'remember_me_enabled' => true,
            'remember_me_parameter' => '_remember_me',
            'remember_me_checked' => true,
            'remember_me_label' => 'Remember me',
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
    }
}