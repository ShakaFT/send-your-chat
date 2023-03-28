<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\Friend\AddFriendDto;
use App\Entity\AbstractEntity;
use App\Entity\Friend;
use App\Entity\User;
use App\Repository\FriendRepository;

class FriendService extends AbstractEntityService
{

    public function __construct(FriendRepository $friendRepository)
    {
        parent::__construct($friendRepository);
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

        // if ($user->getFriends()->contains($entity->getFriend())) {
        //     return "Vous êtes déjà amis avec cet utilisateur.";
        // }

        // $entity->setFriend($user);
        return parent::add($dto, $entity);
    }
}