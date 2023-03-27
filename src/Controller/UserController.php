<?php

namespace App\Controller;

use App\DTO\UserDto;
use App\Entity\User;
use App\Form\UserType;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
	private UserService $userService;

	public function __construct(UserService $userService)
	{
		$this->userService = $userService;
	}

	#[Route('/add', name: 'add_user', methods: ['GET', 'POST'])]
	public function add(Request $request): Response
	{
		/** @var User $user */
		$user = $this->getUser();
		if ($user) {
			return $this->redirectToRoute('index');
		}

		$userDto = new UserDto();

		$form = $this->createForm(UserType::class, $userDto, ['validation_groups' => ['Default', 'add']]);
		$form->handleRequest($request);

		$error = "";

		if ($form->isSubmitted() && $form->isValid()) {
			$user = new User();
			$error = $this->userService->add($userDto, $user);

			if (!$error) return $this->redirectToRoute('add_user');
		}

		return $this->render('users/edit.html.twig', [
			'form' => $form->createView(),
			'isAdd' => true,
			'error' => $error
		]);
	}

	#[Route('/', name: 'edit_user', methods: ['GET', 'POST'])]
	public function edit(Request $request): Response
	{
		/** @var User $user */
		$user = $this->getUser();
		$userDto = new UserDto();

		$form = $this->createForm(UserType::class, $userDto);
		$form->handleRequest($request);

		$error = "";

		if ($form->isSubmitted() && $form->isValid()) {
			$error = $this->userService->update($userDto, $user);
			if (!$error) return $this->redirectToRoute('edit_user');
		}

		return $this->render('users/edit.html.twig', [
			'form' => $form->createView(),
			'isAdd' => false,
			'error' => $error
		]);
	}
}
