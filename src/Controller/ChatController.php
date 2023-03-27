<?php

namespace App\Controller;

use App\DTO\Chat\CreateChatDto;
use App\DTO\Chat\JoinChatDto;
use App\Entity\Chat;
use App\Form\Chat\CreateChatType;
use App\Form\Chat\JoinChatType;
use App\Repository\ChatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/chats")]
class ChatController extends AbstractController
{
    private ChatRepository $chatRepository;

    public function __construct(ChatRepository $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    #[Route('/', name: 'get_chats', methods: ["GET"])]
    public function get_chats(): Response
    {
        return $this->render('chat/chats.html.twig');
    }

    #[Route('/join', name: 'join_chats', methods: ["GET", "POST"])]
    public function join_chats(Request $request): Response
    {
        $chatDto = new JoinChatDto();

        $form = $this->createForm(JoinChatType::class, $chatDto);
        $form->handleRequest($request);

        return $this->render('chat/modal.html.twig', [
            'modalTitle' => 'Créer un chat',
            'confirmationTitle' => 'Créer',
            'form' => $form
        ]);
    }

    #[Route('/create', name: 'create_chats', methods: ["GET", "POST"])]
    public function create_chats(Request $request): Response
    {
        $chatDto = new CreateChatDto();

        $form = $this->createForm(CreateChatType::class, $chatDto);
        $form->handleRequest($request);

        return $this->render('chat/modal.html.twig', [
            'modalTitle' => 'Rejoindre un chat',
            'confirmationTitle' => 'Rejoindre',
            'form' => $form
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
}
