<?php

namespace App\Entity;

use App\DTO\AbstractDto;
use App\DTO\UserDto;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $roles = null;

    public function __construct()
    {
        $this->servers = new ArrayCollection();
        $this->roles = 'ROLE_USER';
        $this->discussions = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
        $this->discussionMessages = new ArrayCollection();
        $this->friends = new ArrayCollection();
    }


    #[ORM\ManyToMany(targetEntity: Server::class, mappedBy: 'users')]
    private Collection $servers;

    #[ORM\OneToMany(mappedBy: 'user1', targetEntity: Discussion::class)]
    private Collection $discussions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ServerMessage::class)]
    private Collection $serverMessages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DiscussionMessage::class)]
    private Collection $discussionMessages;

    #[ORM\OneToMany(mappedBy: 'user1', targetEntity: Friends::class)]
    private Collection $friends;

    /**
	 * @param UserDto $dto
	 */
    public function setFromDto(AbstractDto $dto): void {
        $this->setUsername($dto->username);
        $this->setEmail($dto->email);
        if ($dto->password) {
            $this->setPassword($dto->password);
        }
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Server>
     */
    public function getChats(): Collection
    {
        return $this->servers;
    }

    public function addServer(Server $server): self
    {
        if (!$this->servers->contains($server)) {
            $this->servers->add($server);
            $server->addUser($this);
        }

        return $this;
    }

    public function removeServer(Server $server): self
    {
        if ($this->servers->removeElement($server)) {
            $server->removeUser($this);
        }

        return $this;
    }

    public function getRoles(): array
    {
        return explode(',', $this->roles);
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setUser1($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getUser1() === $this) {
                $discussion->setUser1(null);
            }
        }

        return $this;
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
            $serverMessage->setUser($this);
        }

        return $this;
    }

    public function removeServerMessage(ServerMessage $serverMessage): self
    {
        if ($this->serverMessages->removeElement($serverMessage)) {
            // set the owning side to null (unless already changed)
            if ($serverMessage->getUser() === $this) {
                $serverMessage->setUser(null);
            }
        }

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
            $discussionMessage->setUser($this);
        }

        return $this;
    }

    public function removeDiscussionMessage(DiscussionMessage $discussionMessage): self
    {
        if ($this->discussionMessages->removeElement($discussionMessage)) {
            // set the owning side to null (unless already changed)
            if ($discussionMessage->getUser() === $this) {
                $discussionMessage->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friends>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(Friends $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
            $friend->setUser1($this);
        }

        return $this;
    }

    public function removeFriend(Friends $friend): self
    {
        if ($this->friends->removeElement($friend)) {
            // set the owning side to null (unless already changed)
            if ($friend->getUser1() === $this) {
                $friend->setUser1(null);
            }
        }

        return $this;
    }
}
