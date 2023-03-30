<?php

namespace App\Entity;

use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\Repository\ServerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ServerRepository::class)]
class Server extends AbstractEntity
{
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'servers')]
    private Collection $users;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'server', targetEntity: ServerMessage::class)]
    private Collection $serverMessages;

    #[ORM\Column]
    private ?float $lastInteraction = null;

    #[ORM\ManyToOne(inversedBy: 'ownerServers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'server', targetEntity: ServerToken::class)]
    private Collection $serverTokens;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
        $this->serverTokens = new ArrayCollection();
    }

    public function setFromCreateDto(CreateServerDto $dto): void {
        $this->setName($dto->name);
	}

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return 'server';
    }

    /**
     * @return Collection<int, ServerMessage>
     */
    public function getServerMessages(): Collection
    {
        return $this->serverMessages;
    }

    public function addServerMessage(ServerMessage $serverMessage): self
    {
        if (!$this->serverMessages->contains($serverMessage)) {
            $this->serverMessages->add($serverMessage);
            $serverMessage->setServer($this);
        }

        return $this;
    }

    public function removeServerMessage(ServerMessage $serverMessage): self
    {
        if ($this->serverMessages->removeElement($serverMessage)) {
            // set the owning side to null (unless already changed)
            if ($serverMessage->getServer() === $this) {
                $serverMessage->setServer(null);
            }
        }

        return $this;
    }

    public function getLastInteraction(): ?float
    {
        return $this->lastInteraction;
    }

    public function setLastInteraction(float $lastInteraction): self
    {
        $this->lastInteraction = $lastInteraction;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, ServerToken>
     */
    public function getServerTokens(): Collection
    {
        return $this->serverTokens;
    }

    public function addServerToken(ServerToken $serverToken): self
    {
        if (!$this->serverTokens->contains($serverToken)) {
            $this->serverTokens->add($serverToken);
            $serverToken->setServer($this);
        }

        return $this;
    }

    public function removeServerToken(ServerToken $serverToken): self
    {
        if ($this->serverTokens->removeElement($serverToken)) {
            // set the owning side to null (unless already changed)
            if ($serverToken->getServer() === $this) {
                $serverToken->setServer(null);
            }
        }

        return $this;
    }
}
