<?php

namespace App\Services;

use App\Repository\MessageRepository;

class MessageService extends AbstractEntityService {

	public function __construct(MessageRepository $messageRepository) {
		parent::__construct($messageRepository);
	}

}
