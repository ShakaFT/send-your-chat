<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\Friends\AddFriendDto;
use App\Entity\AbstractEntity;
use App\Entity\Friend;
use App\Entity\User;
use App\Repository\FriendsRepository;
use App\Repository\UserRepository;

class FriendsService extends AbstractEntityService
{

    public function __construct(FriendsRepository $friendsRepository)
    {
        parent::__construct($friendsRepository);
    }

    /**
     * @param AddFriendDto $dto
     * @param Friend $entity
     */
    public function add(AbstractDto $dto, AbstractEntity $entity): string
    {
        $error = "";

        /** @var User $user */
        try {
            $user = $this->repository->findByUsername($dto->username)[0];
        } catch (\Exception) {
            return "L'utilisateur n'existe pas.";
        }

        if ($user->getFriends()->contains($entity->getFriend())) {
            return "Vous êtes déjà amis avec cet utilisateur.";
        }

        $entity->setFriend($user);
        return parent::add($dto, $entity);
    }
}