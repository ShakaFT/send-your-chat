<?php

namespace App\Services;

use App\DTO\Discussion\CreateDiscussionDto;
use App\Entity\AbstractEntity;
use App\Entity\Discussion;
use App\Entity\User;
use App\Repository\DiscussionRepository;
use App\Services\UserService;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Request;

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
	public function create(AbstractEntity $entity, User $currentUser, Request $request): Discussion
	{
		$user = $this->userService->getByUsername($request->query->get("username"));

		$discussion1 = $this->repository->createQueryBuilder('discussion')
			->where('discussion.user1 = :user1_id')
			->andWhere('discussion.user2 = :user2_id')
			->setParameters(['user1_id'=> $currentUser->getId(), 'user2_id' => $user->getId()])
			->getQuery()
			->execute();

		$discussion2 = $this->repository->createQueryBuilder('discussion')
			->where('discussion.user1 = :user1_id')
			->andWhere('discussion.user2 = :user2_id')
			->setParameters(['user1_id'=> $user->getId(), 'user2_id' => $currentUser->getId()])
			->getQuery()
			->execute();

		if ($discussion1) {
			return $discussion1[0];
		}

		if ($discussion2) {
			return $discussion2[0];
		}


		$now = new DateTimeImmutable();
		$entity->setLastInteraction($now->getTimestamp());
		$entity->setUser1($currentUser);
		$entity->setUser2($user);
		$this->repository->save($entity, true);

		return $entity;
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
