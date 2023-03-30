<?php

namespace App\Controller;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
	#[Route("/login", name: "security_login")]
	public function login(AuthenticationUtils $authenticationUtils): Response
	{
		/** @var User $user */
		$user = $this->getUser();
		if ($user) {
			return $this->redirectToRoute('index');
		}

		return $this->render('security/login.html.twig', [
			'last_username' => $authenticationUtils->getLastUsername(),
			'error' => $authenticationUtils->getLastAuthenticationError(),
		]);
	}

	/**
	 * @throws Exception
	 */
	#[Route("/logout", name: "security_logout")]
	public function logout(): void
	{
	}
}
