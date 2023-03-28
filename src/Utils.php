<?php

namespace App;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Utils
{
    function chatsRender(Request $request, User $currentUser, FormInterface $form = null): array
    {        
        $chats = $currentUser->getChats();
        return [
            'background' => 'chats',
            'chats' => $chats,
            'currentChat' => $this->getCurrentChat($request, $chats),
            'formMessage' => $form,
            'user' => $currentUser,
        ];
    }

    function getCurrentChat(Request $request, $chats) {
        return $request->query->get("currentChat") ?? strval($chats ? $chats[0]->getId() : "");
    }
}
