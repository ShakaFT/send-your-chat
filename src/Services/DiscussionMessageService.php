<?php

namespace App\Services;

use App\Entity\AbstractEntity;
use App\Entity\DiscussionMessage;
use App\Entity\User;
use App\Repository\DiscussionMessageRepository;
use DateTimeImmutable;

class DiscussionMessageService extends AbstractEntityService
{

	public function __construct(DiscussionMessageRepository $discussionMessageRepository)
	{
		parent::__construct($discussionMessageRepository);
	}

	/**
	 * @param DiscussionMessage $entity
	 */
	public function send(AbstractEntity $entity, User $currentUser)
	{
		if (!$entity->getContent()) {
			// We can't send empty message.
			return;
		}
		
		$date = new DateTimeImmutable();
		$entity->setTimestamp($date->getTimestamp());
		$entity->setUser($currentUser);
		$this->repository->save($entity, true);
	}
}
