<?php

namespace App\Controller;

use App\DTO\Discussion\CreateDiscussionDto;
use App\Entity\Discussion;
use App\Utils;
use App\Form\Discussion\CreateDiscussionType;
use App\Services\DiscussionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/discussion")]
class DiscussionController extends AbstractController
{
    private DiscussionService $discussionService;
    private Utils $utils;

    public function __construct(DiscussionService $discussionService, Utils $utils)
    {
        $this->discussionService = $discussionService;
        $this->utils = $utils;
    }

    #[Route('/create', name: 'create_discussion', methods: ["GET", "POST"])]
    public function create_discussion(Request $request): Response
    {
        $error = "";
        $discussionDto = new CreateDiscussionDto();

        $form = $this->createForm(CreateDiscussionType::class, $discussionDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discussion = new Discussion();
            $error = $this->discussionService->create($discussionDto, $discussion, $this->getUser());
            if (!$error) return $this->redirectToRoute('get_chats');
        }

        return $this->render('shared/modal.html.twig', [
            ...$this->utils->chatsRender($request, $this->getUser()),
            'confirmationTitle' => 'Créer',
            'error' => $error,
            'form' => $form,
            'modalTitle' => 'Créer une nouvelle discussion avec un ami',
            'pathCanceled' => 'get_chats',
        ]);
    }
}
