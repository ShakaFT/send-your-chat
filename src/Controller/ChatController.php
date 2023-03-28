<?php

namespace App\Controller;

use App\DTO\Chat\CreateChatDto;
use App\DTO\Chat\JoinChatDto;
use App\Entity\Chat;
use App\Entity\User;
use App\Utils;
use App\Form\Chat\CreateChatType;
use App\Form\Chat\JoinChatType;
use App\Services\ChatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/chats")]
class ChatController extends AbstractController
{
    private ChatService $chatService;
    private Utils $utils;

    public function __construct(ChatService $chatService, Utils $utils)
    {
        $this->chatService = $chatService;
        $this->utils = $utils;
    }

    #[Route('/', name: 'get_chats', methods: ["GET"])]
    public function get_chats(Request $request): Response
    {
        return $this->render('chat/chats.html.twig', [
            ...$this->utils->chats_render($request, $this->getUser()),
        ]);
    }

    #[Route('/join', name: 'join_chats', methods: ["GET", "POST"])]
    public function join_chats(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $chats = $currentUser->getChats();

        $chatDto = new JoinChatDto();
        $error = "";

        $form = $this->createForm(JoinChatType::class, $chatDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->chatService->join($chatDto, $this->getUser());
            if (!$error) return $this->redirectToRoute('get_chats');
        }

        return $this->render('shared/modal.html.twig', [
            ...$this->utils->chats_render($request, $this->getUser()),
            'confirmationTitle' => 'Rejoindre',
            'error' => $error,
            'form' => $form,
            'modalTitle' => 'Rejoindre un chat',
        ]);
    }

    #[Route('/create', name: 'create_chats', methods: ["GET", "POST"])]
    public function create_chats(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $chats = $currentUser->getChats();

        $chatDto = new CreateChatDto();

        $form = $this->createForm(CreateChatType::class, $chatDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chat = new Chat();
            $chat->addUser($this->getUser());
            $error = $this->chatService->add($chatDto, $chat);

            if (!$error) return $this->redirectToRoute('get_chats');
        }

        $test = [];

        return $this->render('shared/modal.html.twig', [
            ...$this->utils->chats_render($request, $this->getUser()),
            'confirmationTitle' => 'Créer',
            'error' => '',
            'form' => $form,
            'modalTitle' => 'Créer un chat',
        ]);
    }

    #[Route('/update', name: 'update_chats', methods: ["GET", "POST"])]
    public function update_chats(): Response
    {
        return $this->render('chat/chats.html.twig', [
            'controller_name' => 'UpdateChats',
        ]);
    }

    #[Route('/delete', name: 'delete_chats', methods: ["GET", "POST"])]
    public function delete_chats(): Response
    {
        return $this->render('chat/chats.html.twig', [
            'controller_name' => 'DeleteChats',
        ]);
    }

    #[Route('/settings', name: 'settings_chats', methods: ["GET", "POST"])]
    public function settings_chats(): Response
    {
        return $this->render('chat/settings.html.twig', [
            'controller_name' => 'SettingsChats',
        ]);
    }
}
