<?php

namespace App\DTO;

use App\DTO\AbstractDto;
use App\Entity\AbstractEntity;
use App\Entity\ServerMessage;
use Symfony\Component\Validator\Constraints as Assert;

class SendMessageDto extends AbstractDto
{

	#[Assert\NotBlank]
	public string $message;

	/**
	 * @param ServerMessage $serverMessage
	 */
	public function setFromEntity(AbstractEntity $serverMessage): void {}
}
