<?php

namespace App\DTO\Friend;

use App\DTO\AbstractDto;
use App\Entity\AbstractEntity;
use App\Entity\Friend;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class AddFriendDto extends AbstractDto
{

    #[Assert\NotBlank]
    public string $username;

    /**
     * @param Friend $friend
     */
    public function setFromEntity(AbstractEntity $friend): void
    {
        $this->username = $friend->getUser2()->getUsername();
    }

    /**
     * @param User $user
     */
    public function setFromUser(AbstractEntity $user): void
    {
        $this->username = $user->getUsername();
    }
}
