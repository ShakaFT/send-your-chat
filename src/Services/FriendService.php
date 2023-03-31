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
    public function __construct(FriendRepository $friendRepository, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
		parent::__construct($friendRepository);
    }

    /**
     * @param Friend $entity
     */
    public function acceptRequest(AbstractEntity $entity): void
    {
        $entity->setAccepted(true);
        $this->repository->save($entity, true);
    }

    /**
     * @param AddFriendDto $dto
     * @param Friend $entity
     */
    public function addFriend(AbstractDto $dto, AbstractEntity $entity, User $currentUser): string
    {
        $error = "";

        $users = $this->userRepository->findByUsername($dto->username);
        if (!$users || $users[0]->isDeleted()) {
            return "L'utilisateur n'existe pas.";
        }

        if ($dto->username === $currentUser->getUsername()) {
            return "Vous ne pouvez pas être ami avec vous même.";
        }

        foreach ($currentUser->getFriends() as $friend) {
            if ($friend->getReceiver()->getUsername() === $dto->username || $friend->getSender()->getUsername() === $dto->username) {
                return "Vous êtes déjà ami avec cet utilisateur.";
            }
        }

        $entity->setSender($currentUser);
        $entity->setReceiver($users[0]);
        $entity->setAccepted(false);

        $this->repository->save($entity, true);
        return '';
    }

    public function getByUsername(string $friendName, User $currentUser)
    {
        foreach ($currentUser->getFriends() as $friend) {
            if ($friend->getReceiver()->getUsername() === $friendName || $friend->getSender()->getUsername() === $friendName) {
                return $friend;
            }
        }
    }
}
