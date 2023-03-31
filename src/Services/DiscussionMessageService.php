<?php

namespace App\Services;

use App\Entity\AbstractEntity;
use App\Entity\Discussion;
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

	public function getMessages(Discussion $discussion) {
		$query = $this->repository->createQueryBuilder('message')
			->where('message.discussion = :discussion_id')
			->setParameter('discussion_id', $discussion->getId())
			->getQuery()
			->execute();

		$result = [];
		foreach ($query as $message) {
			array_push($result, [
				'avatar' => $message->getUser()->getAvatar(),
				'content' => $message->getContent(),
				'timeSinceNow' => $message->getTimeSinceNow(),
				'userIsDeleted' => $message->getUser()->isDeleted(),
				'userId' => $message->getUser()->getId(),
				'username' => $message->getUser()->getUsername(),
			]);
		}
		return $result;
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
