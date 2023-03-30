<?php

namespace App\Entity;

use App\Repository\ServerTokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerTokenRepository::class)]
class ServerToken extends AbstractEntity
{
    #[ORM\Column]
    private ?int $token = null;

    #[ORM\Column]
    private ?float $expirationTimestamp = null;

    #[ORM\ManyToOne(inversedBy: 'serverTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Server $server = null;

    public function __construct()
    {
        $this->token = random_int(100000, 999999);
        $now = new DateTimeImmutable();
        $this->expirationTimestamp = $now->getTimestamp() + 300; // 5 minutes
    }

    public function getToken(): ?int
    {
        return $this->token;
    }

    public function setToken(int $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpirationTimestamp(): ?float
    {
        return $this->expirationTimestamp;
    }

    public function setExpirationTimestamp(float $expirationTimestamp): self
    {
        $this->expirationTimestamp = $expirationTimestamp;

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
}
