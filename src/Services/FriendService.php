<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\Friend\AddFriendDto;
use App\Entity\AbstractEntity;
use App\Entity\Friend;
use App\Entity\User;
use App\Repository\FriendRepository;
use App\Repository\UserRepository;

class FriendService extends AbstractEntityService
{

    private UserRepository $userRepository;
    private FriendRepository $friendRepository;
    public function __construct(FriendRepository $friendRepository, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->friendRepository = $friendRepository;
        //parent::__construct($friendRepository);
    }

    /**
     * @param AddFriendDto $dto
     * @param Friend $entity
     */
    public function addFriend(AbstractDto $dto, AbstractEntity $entity, User $currentUser): string
    {
        $error = "";

        /** @var User $user */
        try {
            $user = $this->userRepository->findByUsername($dto->username)[0];
        } catch (\Exception) {
            return "L'utilisateur n'existe pas.";
        }

        if ($dto->username === $currentUser->getUsername()) {
            return "Vous ne pouvez pas être ami avec vous même.";
        }

        foreach ($currentUser->getFriends() as $friend) {
            if($friend->getReceiver()->getUsername() === $dto->username || $friend->getSender()->getUsername() === $dto->username) {
                return "Vous êtes déjà ami avec cet utilisateur.";
            }
        }

        $entity->setSender($currentUser);
        $entity->setReceiver($user);

        $this->friendRepository->save($entity, true);
        return '';
    }
}
