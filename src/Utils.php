<?php

namespace App;

use App\DTO\SendMessageDto;
use App\Form\SendMessageType;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Utils
{
    static function chats_render(Request $request, User $currentUser, FormInterface $form = null): array
    {        
        $chats = $currentUser->getChats();
        return [
            'background' => 'chats',
            'chats' => $chats,
            'currentChat' => $request->query->get("currentChat") ?? strval($chats->getValues() ? $chats[0]->getId() : 0),
            'formMessage' => $form,
            'username' => $currentUser->getUsername(),
        ];
    }
}
