<?php

namespace App\Controller;

use App\DTO\SendMessageDto;
use App\DTO\Server\ChangeServerNameDto;
use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\Entity\Server;
use App\Entity\ServerMessage;
use App\Entity\User;
use App\Form\SendMessageType;
use App\Form\Server\ChangeServerNameType;
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
        $currentChat =  $this->utils->getCurrentChat($request, $user->getChats());

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('send_message', [
                'currentChat' => $currentChat,
                'message' => $sendMessageDto->message,
            ]);
        }

        return $this->render('chat/chats.html.twig', [
            ...$this->utils->chatsRender($request, $user, $form),
            'messages' => $currentChat ? $this->serverService->getById($currentChat)->getServerMessages() : []
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
            ...$this->utils->chatsRender($request, $currentUser),
            'confirmationTitle' => 'Créer',
            'error' => '',
            'form' => $form,
            'modalTitle' => 'Créer un serveur',
            'pathCanceled' => 'get_chats',
        ]);
    }

    #[Route('/server/settings', name: 'server_settings', methods: ["GET", "POST"])]
    public function server_settings(Request $request): Response
    {
        return $this->render('chat/settings.html.twig', [
            ...$this->utils->chatsRender($request, $this->getUser()),
        ]);
    }

    #[Route('/server/update/name', name: 'update_server_name', methods: ["GET", "POST"])]
    public function update_server_name(Request $request): Response
    {
         /** @var User $currentUser */
         $currentUser = $this->getUser();
 
         $dto = new ChangeServerNameDto();
 
         $form = $this->createForm(ChangeServerNameType::class, $dto);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
            //  $server = new Server();
            //  $server->addUser($this->getUser());
            //  $error = $this->serverService->add($serverDto, $server);
 
            //  if (!$error) return $this->redirectToRoute('get_chats');
         }
 
         return $this->render('shared/modal.html.twig', [
             ...$this->utils->chatsRender($request, $currentUser),
             'confirmationTitle' => 'Modifier',
             'error' => '',
             'form' => $form,
             'modalTitle' => 'Modifier le nom du serveur',
             'pathCanceled' => 'server_settings',
         ]);
    }

    #[Route('/server/member-list', name: 'member_list', methods: ["GET", "POST"])]
    public function member_list(Request $request): Response
    {
        return $this->render('chat/settings.html.twig', [
            ...$this->utils->chatsRender($request, $this->getUser()),
        ]);
    }

    #[Route('/server/update/owner', name: 'update_owner', methods: ["GET", "POST"])]
    public function change_owner(Request $request): Response
    {
        return $this->render('chat/settings.html.twig', [
            ...$this->utils->chatsRender($request, $this->getUser()),
        ]);
    }

    #[Route('/delete', name: 'delete_server', methods: ["GET", "POST"])]
    public function delete_server(): Response
    {
        return $this->render('chat/chats.html.twig', [
            'controller_name' => 'DeleteChats',
        ]);
    }
}
