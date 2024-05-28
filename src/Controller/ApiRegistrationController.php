<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiRegistrationController extends AbstractController
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $this->logger->info('Received registration request');

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            $this->logger->error('Invalid JSON');
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName'] ?? null);
        $user->setLastName($data['lastName'] ?? null);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            $this->logger->error('Validation errors: ' . $errorsString);
            return new JsonResponse(['message' => $errorsString], 400);
        }

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['plainPassword']
            )
        );

        $user->setRoles(['ROLE_USER']);
        $entityManager->persist($user);
        $this->logger->info('User persisted');
        $entityManager->flush();
        $this->logger->info('User flushed to database');

        $response = new JsonResponse(['message' => 'User created'], 201);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST');

        return $response;
    }
}


