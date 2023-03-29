<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\Discussion\CreateDiscussionDto;
use App\Entity\AbstractEntity;
use App\Entity\Discussion;
use App\Entity\User;
use App\Repository\DiscussionRepository;
use App\Services\UserService;
use DateTimeImmutable;

class DiscussionService extends AbstractEntityService
{
	private UserService $userService;

	public function __construct(DiscussionRepository $discussionRepository, UserService $userService)
	{
		$this->userService = $userService;
		parent::__construct($discussionRepository);
	}

	/**
	 * @param CreateDiscussionDto $dto
	 * @param Discussion $entity
	 */
	public function create(AbstractDto $dto, AbstractEntity $entity, User $currentUser): string
	{
		$now = new DateTimeImmutable();
		$entity->setLastInteraction($now->getTimestamp());
		$entity->setUser1($currentUser);
		$entity->setUser2($this->userService->getByUsername($dto->friendUsername));
		$this->repository->save($entity, true);

		return '';
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
