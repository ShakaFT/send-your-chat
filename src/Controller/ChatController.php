<?php

namespace App\Controller;

use App\DTO\SendMessageDto;
use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\Entity\Server;
use App\Entity\ServerMessage;
use App\Entity\User;
use App\Form\SendMessageType;
use App\Services\ServerMessageService;
use App\Utils;
use App\Form\Server\CreateServerType;
use App\Form\Server\JoinServerType;
use App\Services\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/chats")]
class ChatController extends AbstractController
{
    private ServerService $serverService;
    private ServerMessageService $serverMessageService;
    private Utils $utils;

    public function __construct(ServerService $serverService, ServerMessageService $serverMessageService, Utils $utils)
    {
        $this->serverService = $serverService;
        $this->serverMessageService = $serverMessageService;
        $this->utils = $utils;
    }

    #[Route('/', name: 'get_chats', methods: ["GET", "POST"])]
    public function get_chats(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $sendMessageDto = new SendMessageDto();
        $form = $this->createForm(SendMessageType::class, $sendMessageDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('send_message', [
                'currentChat' => $this->utils->getCurrentChat($request, $user->getChats()),
                'message' => $sendMessageDto->message,
            ]);
        }

        return $this->render('chat/chats.html.twig', [
            ...$this->utils->chatsRender($request, $user, $form),
        ]);
    }

    #[Route('/send_message', name: 'send_message', methods: ["GET", "POST"])]
    public function send_message(Request $request): Response
    {
        $currentChat = $request->query->get('currentChat');

        $message = new ServerMessage();
        $message->setServer($this->serverService->getById($currentChat));
        $message->setContent($request->query->get('message'));
        $this->serverMessageService->send($message, $this->getUser());

        return $this->redirectToRoute('get_chats', [
            'currentChat' => $currentChat
        ]);
    }

    #[Route('/server/join', name: 'join_server', methods: ["GET", "POST"])]
    public function join_server(Request $request): Response
    {
        $serverDto = new JoinServerDto();
        $error = "";

        $form = $this->createForm(JoinServerType::class, $serverDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->serverService->join($serverDto, $this->getUser());
            if (!$error) return $this->redirectToRoute('get_chats');
        }

        return $this->render('shared/modal.html.twig', [
            ...$this->utils->chatsRender($request, $this->getUser()),
            'confirmationTitle' => 'Rejoindre',
            'error' => $error,
            'form' => $form,
            'modalTitle' => 'Rejoindre un serveur',
        ]);
    }

    #[Route('/server/create', name: 'create_server', methods: ["GET", "POST"])]
    public function create_server(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $chats = $currentUser->getChats();

        $serverDto = new CreateServerDto();

        $form = $this->createForm(CreateServerType::class, $serverDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $server = new Server();
            $server->addUser($this->getUser());
            $error = $this->serverService->add($serverDto, $server);

            if (!$error) return $this->redirectToRoute('get_chats');
        }

        return $this->render('shared/modal.html.twig', [
            ...$this->utils->chatsRender($request, $this->getUser()),
            'confirmationTitle' => 'Créer',
            'error' => '',
            'form' => $form,
            'modalTitle' => 'Créer un serveur',
        ]);
    }


    // #[Route('/server/update', name: 'update_server', methods: ["GET", "POST"])]
    // public function update_server(): Response
    // {
    //     return $this->render('chat/chats.html.twig', [
    //         'controller_name' => 'UpdateChats',
    //     ]);
    // }

    // #[Route('/delete', name: 'delete_server', methods: ["GET", "POST"])]
    // public function delete_server(): Response
    // {
    //     return $this->render('chat/chats.html.twig', [
    //         'controller_name' => 'DeleteChats',
    //     ]);
    // }

    #[Route('/settings', name: 'settings_chat', methods: ["GET", "POST"])]
    public function settings_chat(): Response
    {
        return $this->render('chat/settings.html.twig', [
            'controller_name' => 'SettingsChat',
        ]);
    }
}
