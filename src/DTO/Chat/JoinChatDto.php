<?php

namespace App\DTO\Chat;

use Symfony\Component\Validator\Constraints as Assert;

class JoinChatDto {

	#[Assert\NotBlank]
	public string $token;
}
