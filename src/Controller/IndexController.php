<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
	#[Route("/", name: "index", methods: ["GET"])]
	public function index(): Response
	{
		/** @var User $user */
		$user = $this->getUser();
		if ($user) {
			return $this->redirectToRoute('get_chats');
		}
		return $this->redirectToRoute('security_login');
	}
}
