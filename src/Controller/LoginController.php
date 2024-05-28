<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/api/login', name: 'app_login')]
    public function login(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $email = $requestData['email'];
        $password = $requestData['password'];
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user || !$passwordEncoder->isPasswordValid($user, $password)) {
            // Si l'utilisateur n'existe pas ou que le mot de passe est incorrect, retourner une erreur
            return new JsonResponse(['message' => 'Invalid email or password'], Response::HTTP_UNAUTHORIZED);
        }
        return new JsonResponse(['message' => 'Login successful', 'user' => $user]);
    }

}
