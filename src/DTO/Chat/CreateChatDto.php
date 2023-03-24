<?php

namespace App\DTO\Chat;

use Symfony\Component\Validator\Constraints as Assert;

class CreateChatDto {

	#[Assert\NotBlank]
	public string $name;
}
