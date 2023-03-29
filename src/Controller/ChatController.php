<?php

namespace App\Controller;

use App\DTO\SendMessageDto;
use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\Entity\DiscussionMessage;
use App\Entity\Server;
use App\Entity\ServerMessage;
use App\Entity\User;
use App\Form\SendMessageType;
use App\Services\DiscussionMessageService;
use App\Services\ServerMessageService;
use App\Utils;
use App\Form\Server\CreateServerType;
use App\Form\Server\JoinServerType;
use App\Services\DiscussionService;
use App\Services\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/chats")]
class ChatController extends AbstractController
{
    private DiscussionService $discussionService;
    private ServerService $serverService;
    private DiscussionMessageService $discussionMessageService;
    private ServerMessageService $serverMessageService;
    private Utils $utils;

    public function __construct(DiscussionService $discussionService, ServerService $serverService, DiscussionMessageService $discussionMessageService, ServerMessageService $serverMessageService, Utils $utils)
    {
        $this->discussionService = $discussionService;
        $this->serverService = $serverService;
        $this->discussionMessageService = $discussionMessageService;
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
        $currentChat =  $this->utils->getCurrentChat($request, $user->getChats());

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('send_message', [
                'currentChat' => $currentChat[0],
                'typeChat' => $currentChat[1],
                'message' => $sendMessageDto->message,
            ]);
        }

        $messages = [];
        if (!$currentChat[1] === 'discussion') {
            $messages = $this->discussionService->getById($currentChat[0])->getDiscussionMessages();
        } else if ($currentChat[1] === 'server') {
            $messages = $this->serverService->getById($currentChat[0])->getServerMessages();
        }

        return $this->render('chat/chats.html.twig', [
            ...$this->utils->chatsRender($request, $user, $form),
            'messages' => $messages,
        ]);
    }

    #[Route('/send_message', name: 'send_message', methods: ["GET", "POST"])]
    public function send_message(Request $request): Response
    {
        $currentUser = $this->getUser();
        $currentChat = $request->query->get('currentChat');
        $typeChat = $request->query->get('typeChat');

        if ($typeChat === 'discussion') {
            $message = new DiscussionMessage();
            $message->setDiscussion($this->discussionService->getById($currentChat));
            $message->setContent($request->query->get('message'));
            $this->discussionMessageService->send($message, $currentUser);
            $this->discussionService->refreshLastInteraction($currentChat);
        } else {
            $message = new ServerMessage();
            $message->setServer($this->serverService->getById($currentChat));
            $message->setContent($request->query->get('message'));
            $this->serverMessageService->send($message, $currentUser);
            $this->serverService->refreshLastInteraction($currentChat);
        }

        return $this->redirectToRoute('get_chats', [
            'currentChat' => $currentChat,
            'typeChat' => $typeChat
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
            'pathCanceled' => 'get_chats',
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
            'pathCanceled' => 'get_chats',
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
