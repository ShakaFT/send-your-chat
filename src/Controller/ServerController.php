<?php

namespace App\Controller;

use App\DTO\Server\ChangeServerNameDto;
use App\DTO\Server\UpdateServerOwnerDto;
use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\Entity\Server;
use App\Entity\User;
use App\Utils;
use App\Form\Server\CreateServerType;
use App\Form\Server\ChangeServerNameType;
use App\Form\Server\UpdateServerOwnerType;
use App\Form\Server\JoinServerType;
use App\Services\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/server")]
class ServerController extends AbstractController
{
    private ServerService $serverService;
    private Utils $utils;

    public function __construct(ServerService $serverService, Utils $utils)
    {
        $this->serverService = $serverService;
        $this->utils = $utils;
    }

    #[Route('/create', name: 'create_server', methods: ["GET", "POST"])]
    public function create_server(Request $request): Response
    {
        $serverDto = new CreateServerDto();

        $form = $this->createForm(CreateServerType::class, $serverDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $server = new Server();
            $server->addUser($this->getUser());
            $this->serverService->add($serverDto, $server);

            return $this->redirectToRoute('get_chats');
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

    #[Route('/join', name: 'join_server', methods: ["GET", "POST"])]
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

    #[Route('/settings', name: 'server_settings', methods: ["GET", "POST"])]
    public function server_settings(Request $request): Response
    {
        $currentChat = $request->query->get('currentChat');
        $serverToken = $this->serverService->getServerToken($currentChat);

        return $this->render('chat/settings.html.twig', [
            ...$this->utils->chatsRender($request, $this->getUser()),
            'serverToken' => $serverToken,
        ]);
    }

    #[Route('/update/name', name: 'update_server_name', methods: ["GET", "POST"])]
    public function update_server_name(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $dto = new ChangeServerNameDto();
        $currentChat = $this->utils->getCurrentChat($request, $currentUser->getChats());

        $error = "";

        $form = $this->createForm(ChangeServerNameType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->serverService->changeName($currentChat[0], $dto);

            if (!$error) return $this->redirectToRoute('get_chats', [
                'currentChat' => $currentChat[0],
                'typeChat' => $currentChat[1],
            ]);
        }

        return $this->render('shared/modal.html.twig', [
            ...$this->utils->chatsRender($request, $currentUser),
            'confirmationTitle' => 'Modifier',
            'error' => $error,
            'form' => $form,
            'modalTitle' => 'Modifier le nom du serveur',
            'pathCanceled' => 'server_settings',
        ]);
    }

    #[Route('/member-list', name: 'member_list', methods: ["GET", "POST"])]
    public function member_list(Request $request): Response
    {
        $currentUser = $this->getUser();
        $currentChat = $request->query->get('currentChat');
        $members = $this->serverService->getMembers($currentChat);

        return $this->render('chat/list.html.twig', [
            ...$this->utils->chatsRender($request, $currentUser),
            'modalTitle' => 'Liste des membres',
            'members' => $members,
        ]);
    }

    #[Route('/update/owner', name: 'update_server_owner', methods: ["GET", "POST"])]
    public function update_server_owner(Request $request): Response
    {
         /** @var User $currentUser */
         $currentUser = $this->getUser();

         $dto = new UpdateServerOwnerDto();
         $currentChat = $this->utils->getCurrentChat($request, $currentUser->getChats());
 
         $error = "";
 
         $form = $this->createForm(UpdateServerOwnerType::class, $dto);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
             $error = $this->serverService->changeName($currentChat[0], $dto);
 
             if (!$error) return $this->redirectToRoute('get_chats', [
                 'currentChat' => $currentChat[0],
                 'typeChat' => $currentChat[1],
             ]);
         }
 
         return $this->render('shared/modal.html.twig', [
             ...$this->utils->chatsRender($request, $currentUser),
             'confirmationTitle' => 'Transférer',
             'error' => $error,
             'form' => $form,
             'modalTitle' => 'Transférer la propriété du serveur',
             'pathCanceled' => 'server_settings',
         ]);
    }

    #[Route('/delete', name: 'delete_server', methods: ["GET", "POST"])]
    public function delete_server(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $currentChat = $this->utils->getCurrentChat($request, $currentUser->getChats());

        if ($request->query->get('confirm') === "true") {
			$this->serverService->delete($this->serverService->getById($currentChat[0]));
			return $this->redirectToRoute("get_chats");
		}
		return $this->render('shared/alert.html.twig', [
			...$this->utils->chatsRender($request, $this->getUser()),
			'alertTitle' => 'Supprimer le serveur',
			'background' => 'chats',
			'confirmationTitle' => 'Supprimer',
			'alertContent' => 'Voulez vous vraiment supprimer le serveur ?',
			'submitRoute' => 'delete_server',
			'pathCanceled' =>'server_settings',
			'submitParams' => [
                'currentChat' => $currentChat[0],
                'typeChat' => $currentChat[1],
				'confirm' => 'true',
			]
		]);
    }
}