<?php
// src/Controller/ApiRegistrationController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiRegistrationController extends AbstractController
{
	#[Route('/api/register', name: 'api_register', methods: ['POST'])]
	public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
	{
		// var_dump($_POST);
		// die;
		$data = json_decode($request->getContent(), true);

		if (!$data) {
			return new JsonResponse(['message' => 'Invalid JSON'], 400);
		}

		$user = new User();
		$user->setEmail($data['email'] ?? null);
		$user->setPassword($data['plainPassword'] ?? null);

		$errors = $validator->validate($user);
		if (count($errors) > 0) {
			$errorsString = (string) $errors;

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
		$entityManager->flush();

		$response = new JsonResponse(['message' => 'User created'], 201);

		// Ajoutez les en-têtes CORS
		$response->headers->set('Access-Control-Allow-Origin', '*');
		$response->headers->set('Access-Control-Allow-Methods', 'POST');

		// Retournez la réponse
		return $response;
	}
}
