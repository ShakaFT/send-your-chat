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
        $discussion = $this->discussionService->create(new Discussion(), $this->getUser(), $request);
        return $this->redirectToRoute('get_chats', [
            'currentChat' => $discussion->getId(),
            'typeChat' => 'discussion',
        ]);
    }
}
