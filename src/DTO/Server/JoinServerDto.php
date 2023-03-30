<?php

namespace App\DTO\Server;

use App\DTO\AbstractDto;
use App\Entity\AbstractEntity;
use App\Entity\Server;
use Symfony\Component\Validator\Constraints as Assert;

class JoinServerDto extends AbstractDto {

	#[Assert\NotBlank]
	public string $token;

	/**
	 * @param Server $server
	 */
	public function setFromEntity(AbstractEntity $server): void {}
}
