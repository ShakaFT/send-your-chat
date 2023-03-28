<?php

namespace App;

use App\Entity\Server;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Utils
{
    function chatsRender(Request $request, User $currentUser, FormInterface $form = null): array
    {        
        $chats = $currentUser->getChats();
        $currentChat = $this->getCurrentChat($request, $chats);
        return [
            'background' => 'chats',
            'chats' => $chats,
            'currentChat' => $currentChat[0],
            'formMessage' => $form,
            'typeChat' => $currentChat[1],
            'user' => $currentUser,
        ];
    }

    function getCurrentChat(Request $request, $chats) {
        // [0] -> current chat id
        return [
            $request->query->get("currentChat") ?? strval($chats ? $chats[0]->getId() : ""),
            $request->query->get("typeChat") ?? $chats ? ($chats[0]->getType() ) : ""
        ];
    }
}
