<?php

namespace App\DTO;

use App\DTO\AbstractDto;
use App\Entity\AbstractEntity;
use App\Entity\ServerMessage;

class SendMessageDto extends AbstractDto
{
	public string $message;

	/**
	 * @param ServerMessage $serverMessage
	 */
	public function setFromEntity(AbstractEntity $serverMessage): void {}
}
