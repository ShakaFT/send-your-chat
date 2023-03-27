<?php

namespace App\DTO\Chat;

use App\DTO\AbstractDto;
use App\Entity\AbstractEntity;
use App\Entity\Chat;
use Symfony\Component\Validator\Constraints as Assert;

class JoinChatDto extends AbstractDto {

	#[Assert\NotBlank]
	public string $token;

	/**
	 * @param Chat $chat
	 */
	public function setFromEntity(AbstractEntity $chat): void
	{
	}
}
