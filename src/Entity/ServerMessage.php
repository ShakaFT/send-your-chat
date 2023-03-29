<?php

namespace App\Entity;

use App\Repository\ServerMessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerMessageRepository::class)]
class ServerMessage extends AbstractEntity
{

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'serverMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Server $server = null;

    #[ORM\ManyToOne(inversedBy: 'serverMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $timestamp = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(?Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTimestamp(): ?float
    {
        return $this->timestamp;
    }

    public function getTimeSinceNow(): string
    {

        $now = new DateTimeImmutable();
        $nowAtMidnight = new DateTimeImmutable('today midnight');

        $messageTimestampSinceNow = ($now->getTimestamp() - $this->timestamp);
        $timestampSinceNow = ($now->getTimestamp() - $nowAtMidnight->getTimestamp());

        if ($messageTimestampSinceNow < 60) {
            return "À l'instant";
        }

        if ($messageTimestampSinceNow > $timestampSinceNow) {
            return date('d-m-Y H:i', $this->timestamp);
        }

        if ($messageTimestampSinceNow < 3600) {
            $timeSince = sprintf("%d", date('i', $messageTimestampSinceNow));
            return "Il y a $timeSince min";
        }

        $timeSince = sprintf("%d", date('H', $messageTimestampSinceNow));
        return "Il y a {$timeSince}h";
    }

    public function setTimestamp(float $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
