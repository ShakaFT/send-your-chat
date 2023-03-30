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

    #[ORM\Column(length: 255)]
    private ?string $token = null;

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

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
    }

    public function setFromCreateDto(CreateServerDto $dto): void {
        $this->setName($dto->name);
        $this->setToken(Uuid::v4());
	}

    public function setFromJoinDto(JoinServerDto $dto): void {
        $this->setToken($dto->token);
	}

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
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
}
