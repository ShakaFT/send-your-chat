<?php

namespace App\Entity;

use App\DTO\AbstractDto;
use App\DTO\UserDto;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;
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

    #[ORM\Column(length: 255)]
    private ?string $avatar = null;

    #[ORM\ManyToMany(targetEntity: Server::class, mappedBy: 'users')]
    private Collection $servers;

    #[ORM\OneToMany(mappedBy: 'user1', targetEntity: Discussion::class)]
    private Collection $discussions1;

    #[ORM\OneToMany(mappedBy: 'user2', targetEntity: Discussion::class)]
    private Collection $discussions2;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ServerMessage::class)]
    private Collection $serverMessages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DiscussionMessage::class)]
    private Collection $discussionMessages;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Friend::class)]
    private Collection $friendsSender;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Friend::class)]
    private Collection $friendsReceiver;

    public function __construct()
    {
        $this->servers = new ArrayCollection();
        $this->roles = 'ROLE_USER';
        $this->avatar = '';
        $this->discussions1 = new ArrayCollection();
        $this->discussions2 = new ArrayCollection();
        $this->serverMessages = new ArrayCollection();
        $this->discussionMessages = new ArrayCollection();
        $this->friendsSender = new ArrayCollection();
        $this->friendsReceiver = new ArrayCollection();
    }

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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return array<int, Server>
     */
    public function getChats(): array
    {
        $chats = [...$this->servers, ...$this->getDiscussions()];
        if ($chats) usort($chats, function($a, $b) {return strcmp($b->getLastInteraction(), $a->getLastInteraction());});
        return $chats;
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
     * @return array<int, Discussion>
     */
    public function getDiscussions(): array
    {
        return [...$this->discussions1, ...$this->discussions2];
    }

    // public function addDiscussion(Discussion $discussion): self
    // {
    //     if (!$this->discussions->contains($discussion)) {
    //         $this->discussions->add($discussion);
    //         $discussion->setUser1($this);
    //     }

    //     return $this;
    // }

    // public function removeDiscussion(Discussion $discussion): self
    // {
    //     if ($this->discussions->removeElement($discussion)) {
    //         // set the owning side to null (unless already changed)
    //         if ($discussion->getUser1() === $this) {
    //             $discussion->setUser1(null);
    //         }
    //     }

    //     return $this;
    // }

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
     * @return Collection<int, Friend>
     */
    public function getFriendsReceiver(): Collection
    {
        return $this->friendsReceiver;
    }

    public function addFriendReceiver(Friend $friendReceiver): self
    {
        if (!$this->friendsReceiver->contains($friendReceiver)) {
            $this->friendsReceiver->add($friendReceiver);
            $friendReceiver->setReceiver($this);
        }

        return $this;
    }

    public function removeFriend(Friend $friendReceiver): self
    {
        if ($this->friendsReceiver->removeElement($friendReceiver)) {
            // set the owning side to null (unless already changed)
            if ($friendReceiver->getReceiver() === $this) {
                $friendReceiver->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friend>
     */
    public function getFriendsSender(): Collection
    {
        return $this->friendsSender;
    }

    public function addFriendSender(Friend $friendSender): self
    {
        if (!$this->friendsSender->contains($friendSender)) {
            $this->friendsSender->add($friendSender);
            $friendSender->setReceiver($this);
        }

        return $this;
    }

    public function removeFriendSender(Friend $friendSender): self
    {
        if ($this->friendsSender->removeElement($friendSender)) {
            // set the owning side to null (unless already changed)
            if ($friendSender->getReceiver() === $this) {
                $friendSender->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friend>
     */
    public function getFriends() : Collection {
        return new ArrayCollection(
            array_merge($this->friendsReceiver->toArray(), $this->friendsSender->toArray())
        );
    }
}
