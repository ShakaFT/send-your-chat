<?php

namespace App;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class Utils
{
    static function chats_render(Request $request, User $currentUser): array
    {
        $chats = $currentUser->getChats();
        return [
            'background' => 'chats',
            'chats' => $chats,
            'currentChat' => $request->query->get('currentChat') ?? strval($chats->getValues() ? $chats[0]->getId() : 0),
            'username' => $currentUser->getUsername(),
        ];
    }
}
