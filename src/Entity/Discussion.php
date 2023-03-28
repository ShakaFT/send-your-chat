<?php

namespace App\Entity;

use App\Repository\DiscussionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionRepository::class)]
class Discussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'discussions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user1 = null;

    #[ORM\ManyToOne(inversedBy: 'discussions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user2 = null;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: DiscussionMessage::class)]
    private Collection $discussionMessages;

    public function __construct()
    {
        $this->discussionMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, DiscussionMessage>
     */
    public function getDiscussionMessages(): Collection
    {
        return $this->discussionMessages;
    }

    public function addDiscussionMessage(DiscussionMessage $discussionMessage): self
    {
        if (!$this->discussionMessages->contains($discussionMessage)) {
            $this->discussionMessages->add($discussionMessage);
            $discussionMessage->setDiscussion($this);
        }

        return $this;
    }

    public function removeDiscussionMessage(DiscussionMessage $discussionMessage): self
    {
        if ($this->discussionMessages->removeElement($discussionMessage)) {
            // set the owning side to null (unless already changed)
            if ($discussionMessage->getDiscussion() === $this) {
                $discussionMessage->setDiscussion(null);
            }
        }

        return $this;
    }
}
