<?php

namespace App\Services;

use App\Entity\Discussion;
use App\Repository\DiscussionRepository;
use DateTimeImmutable;

class DiscussionService extends AbstractEntityService
{

	public function __construct(DiscussionRepository $discussionRepository)
	{
		parent::__construct($discussionRepository);
	}

	public function refreshLastInteraction(int $discussionId) {
		$now = new DateTimeImmutable();

		$discussion = $this->getById($discussionId);
		$discussion->setLastInteraction($now->getTimestamp());
		$this->repository->save($discussion, true);
	}

	public function getById(int $id) : Discussion
	{
		return $this->repository->findById($id)[0];
	}
}
