<?php

namespace App\Services;

use App\Repository\ChatRepository;

class UserService extends AbstractEntityService {

	public function __construct(ChatRepository $chatRepository) {
		parent::__construct($chatRepository);
	}

}
