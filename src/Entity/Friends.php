<?php

namespace App\Entity;

use App\DTO\Friends\AddFriendDto;
use App\Repository\FriendsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendsRepository::class)]
class Friends extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user1 = null;

    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user2 = null;

    public function __construct()
    {
        $this->user1 = User::class;
        $this->user2 = User::class;
    }

    public function setFromAddDto(AddFriendDto $dto, User $user): void
    {
        $this->setUser1($user);
        $this->setUser2($dto->friend);
    }

    public function getUser1(): ?User
    {
        return $this->user1;
    }

    public function setUser1(?User $user1): self
    {
        $this->user1 = $user1;

        return $this;
    }

    public function getUser2(): ?User
    {
        return $this->user2;
    }

    public function setUser2(?User $user2): self
    {
        $this->user2 = $user2;

        return $this;
    }
}
