<?php

namespace App\Entity;

use App\DTO\AbstractDto;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractEntity {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: "integer")]
	protected int $id;

	public function getId(): int
	{
		return $this->id;
	}

	public function setFromDto(AbstractDto $dto): void {
	}
}
