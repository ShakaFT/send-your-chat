<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/friends')]
class FriendController extends AbstractController
{
    #[Route('/', name: 'get_friends')]
    public function index(): Response
    {
        return $this->render('friend/friends.html.twig', [

        ]);
    }
}
