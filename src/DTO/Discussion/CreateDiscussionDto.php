<?php

namespace App\DTO\Discussion;

use App\DTO\AbstractDto;
use App\Entity\AbstractEntity;
use App\Entity\Discussion;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDiscussionDto extends AbstractDto
{

	#[Assert\NotBlank]
	public string $friendUsername;

	/**
	 * @param Discussion $discussion
	 */
	public function setFromEntity(AbstractEntity $discussion): void
	{
	}
}
