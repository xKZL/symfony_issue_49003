<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/register', name: 'security_register')]
    public function register(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        $email = "test@example.org";
        $password = "symfony";

        if ($this->getUser()) 
            return $this->redirectToRoute('security_logout');
        if( count($entityManager->getRepository(User::class)->findBy(["email" => $email])) )
            return $this->redirectToRoute('security_login');

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('security_login');
    }

    #[Route(path: '/', name: 'security_index')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        return new Response('<html><head></head><body></body></html>');
    }

    #[Route(path: '/login', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout-request', name: 'security_logoutRequest')]
    public function LogoutRequest()
    {
        throw new \Exception("This page should not be displayed.. Firewall should take over during logout process. Please check your configuration..");
    }

    #[Route(path: '/logout', name: 'security_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('security_login');
    }
}
