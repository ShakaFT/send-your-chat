<?php

namespace App;

use App\Entity\User;
use DateTimeImmutable;
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

    function getCurrentChat(Request $request, $chats) : array {
        // [0] -> current chat id
        // [1] -> current chat type
        return [
            $request->query->get("currentChat") ?? strval($chats ? $chats[0]->getId() : ""),
            $request->query->get("typeChat") ?? ($chats ? $chats[0]->getType() : "")
        ];
    }

    public function getTimeSinceNow(float $timestamp): string
    {

        $now = new DateTimeImmutable();
        $nowAtMidnight = new DateTimeImmutable('today midnight');

        $messageTimestampSinceNow = ($now->getTimestamp() - $timestamp);
        $timestampSinceNow = ($now->getTimestamp() - $nowAtMidnight->getTimestamp());

        if ($messageTimestampSinceNow < 60) {
            return "À l'instant";
        }

        if ($messageTimestampSinceNow > $timestampSinceNow) {
            return date('d-m-Y H:i', $timestamp);
        }

        if ($messageTimestampSinceNow < 3600) {
            $timeSince = sprintf("%d", date('i', $messageTimestampSinceNow));
            return "Il y a $timeSince min";
        }

        $timeSince = sprintf("%d", date('H', $messageTimestampSinceNow));
        return "Il y a {$timeSince}h";
    }
}
