<?php

namespace App\Entity;

use App\DTO\Friend\AddFriendDto;
use App\Repository\FriendRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRepository::class)]
class Friend extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'friendsSender')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'friendsReceiver')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    /**
     * @param AddFriendDto $dto
     */

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}
