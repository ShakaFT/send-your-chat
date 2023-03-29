<?php

namespace App\Controller;

use App\DTO\Friend\AddFriendDto;
use App\Entity\Friend;
use App\Entity\User;
use App\Form\AddFriendType;
use App\Services\FriendService;
use App\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/friends')]
class FriendController extends AbstractController
{

    private FriendService $friendService;
    private Utils $utils;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
        $this->utils = new Utils();
    }

    #[Route('/', name: 'get_friends')]
    public function get_friends(Request $request): Response
    {
         /** @var User $user */
         $user = $this->getUser();

        return $this->render('friend/friends.html.twig', [
            'friends' => $user->getFriends(),
            'user' => $user,
            ...$this->utils->chatsRender($request, $user)
        ]);
    }

    #[Route('/add', name: 'add_friend')]
    public function add_friend(Request $request): Response
    {
         /** @var User $user */
         $user = $this->getUser();
         $friendToAdd = new Friend();

         $dto = new AddFriendDto();
         $error = "";

         $form = $this->createForm(AddFriendType::class, $dto);
         $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->friendService->addFriend($dto, $friendToAdd ,$user);
            if (!$error) return $this->redirectToRoute('get_friends');
        }

        return $this->render('/shared/modal.html.twig', [
            ...$this->utils->chatsRender($request, $user),
            'form' => $form,
            'modalTitle' => 'Ajouter un ami',
            'confirmationTitle' => 'Envoyer',
            'pathCanceled' => 'get_friends',
            'error' => $error
        ]);
    }
}
