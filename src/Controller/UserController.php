<?php

namespace App\Controller;

use App\DTO\ResetPasswordDto;
use App\DTO\UserDto;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Form\UserType;
use App\Services\UserService;
use App\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
	private UserService $userService;
	private Utils $utils;

	public function __construct(UserService $userService, Utils $utils)
	{
		$this->userService = $userService;
		$this->utils = $utils;
	}

	#[Route('/add', name: 'add_user', methods: ['GET', 'POST'])]
	public function add(Request $request, Security $security): Response
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

			if (!$error) {
				$security->login($user);
				return $this->redirectToRoute('add_user');
			} 
		}

		return $this->render('users/add.html.twig', [
			'form' => $form->createView(),
			'isAdd' => true,
			'error' => $error
		]);
	}

	#[Route('/update', name: 'edit_user', methods: ['GET', 'POST'])]
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
			if (!$error) return $this->redirectToRoute('get_chats');
		}

		return $this->render('shared/modal.html.twig', [
			...$this->utils->chatsRender($request, $this->getUser()),
			'confirmationTitle' => 'Modifier',
			'error' => $error,
			'background' => 'chats',
			'modalTitle' => 'Modifier le profil',
			'form' => $form,
			'pathCanceled' => 'profile'
		]);
	}

	#[Route('/profile', name: 'profile', methods: ['GET', 'POST'])]
	public function profile(Request $request): Response
	{
		return $this->render('users/modal_profile.html.twig', [
			...$this->utils->chatsRender($request, $this->getUser())
		]);
	}

	#[Route('reset-password', name: 'reset_password', methods: ['GET', 'POST'])]
	public function resetPassword(Request $request): Response
	{
		/** @var User $user */
		$user = $this->getUser();
		$resetPasswordDto = new ResetPasswordDto();

		$form = $this->createForm(ResetPasswordType::class, $resetPasswordDto);
		$form->handleRequest($request);

		$error = "";

		if ($form->isSubmitted() && $form->isValid()) {
			$error = $this->userService->updatePassword($resetPasswordDto, $user);
			if (!$error) return $this->redirectToRoute('get_chats');
		}

		return $this->render('shared/modal.html.twig', [
			...$this->utils->chatsRender($request, $this->getUser()),
			'confirmationTitle' => 'Réinitialiser',
			'error' => $error,
			'background' => 'chats',
			'modalTitle' => 'Réinitialiser le mot de passe',
			'form' => $form,
			'pathCanceled' => 'profile'
		]);
	}

	#[Route('/delete', name: 'delete_user', methods: ['GET', 'POST'])]
	public function deleteAccount(Request $request, Security $security): Response
	{
		if ($request->query->get('confirm') === "true") {
			/** @var User $user */
			$user = $this->getUser();
			$security->logout(false);
			$this->userService->delete($user);
			return $this->redirectToRoute("security_login");
		}
		return $this->render('shared/alert.html.twig', [
			...$this->utils->chatsRender($request, $this->getUser()),
			'alertTitle' => 'Supprimer le compte',
			'background' => 'chats',
			'confirmationTitle' => 'Supprimer',
			'alertContent' => 'Voulez vous vraiment supprimer le compte ?',
			'submitRoute' => 'delete_user'
		]);
	}
}
