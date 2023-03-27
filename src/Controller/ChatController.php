<?php

namespace App\Controller;

use App\DTO\Chat\CreateChatDto;
use App\DTO\Chat\JoinChatDto;
use App\Entity\Chat;
use App\Form\Chat\CreateChatType;
use App\Form\Chat\JoinChatType;
use App\Services\ChatService;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/chats")]
class ChatController extends AbstractController
{
    private ChatService $chatService;
    private MessageRepository $messageRepository;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    #[Route('/', name: 'get_chats', methods: ["GET"])]
    public function get_chats(): Response
    {
        // $messages = $this->messageRepository->findAll();
        return $this->render('chat/chats.html.twig', [
            // 'messages' => $messages
        ]);
    }

    #[Route('/join', name: 'join_chats', methods: ["GET", "POST"])]
    public function join_chats(Request $request): Response
    {
        $chatDto = new JoinChatDto();
        $error = "";

        $form = $this->createForm(JoinChatType::class, $chatDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->chatService->join($chatDto, $this->getUser());
            if (!$error) return $this->redirectToRoute('get_chats');
        }

        return $this->render('chat/modal.html.twig', [
            'modalTitle' => 'Rejoindre un chat',
            'confirmationTitle' => 'Rejoindre',
            'form' => $form,
            'error' => $error
        ]);
    }

    #[Route('/create', name: 'create_chats', methods: ["GET", "POST"])]
    public function create_chats(Request $request): Response
    {
        $chatDto = new CreateChatDto();

        $form = $this->createForm(CreateChatType::class, $chatDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->chatService->add($chatDto, new Chat());

            if (!$error) return $this->redirectToRoute('get_chats');
        }

        return $this->render('chat/modal.html.twig', [
            'modalTitle' => 'Créer un chat',
            'confirmationTitle' => 'Créer',
            'form' => $form,
            'error' => ''
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