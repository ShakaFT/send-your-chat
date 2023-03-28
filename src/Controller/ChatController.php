<?php

namespace App\Controller;

use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\Entity\Server;
use App\Entity\User;
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
    private Utils $utils;

    public function __construct(ServerService $serverService, Utils $utils)
    {
        $this->serverService = $serverService;
        $this->utils = $utils;
    }

    #[Route('/', name: 'get_chats', methods: ["GET"])]
    public function get_chats(Request $request): Response
    {
        return $this->render('chat/chats.html.twig', [
            ...$this->utils->chats_render($request, $this->getUser()),
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
            ...$this->utils->chats_render($request, $this->getUser()),
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
            ...$this->utils->chats_render($request, $this->getUser()),
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
