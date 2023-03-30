<?php

namespace App\Services;

use App\Entity\AbstractEntity;
use App\Entity\Server;
use App\Entity\ServerMessage;
use App\Entity\User;
use App\Repository\ServerMessageRepository;
use DateTimeImmutable;

class ServerMessageService extends AbstractEntityService
{

	public function __construct(ServerMessageRepository $serverMessageRepository)
	{
		parent::__construct($serverMessageRepository);
	}

	/**
	 * @param ServerMessage $entity
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

	public function deleteMessages(Server $server) {
		$this->repository->createQueryBuilder('message')
			->delete()
			->where('message.server = :server_id')
			->setParameter('server_id', $server->getId())
			->getQuery()
			->execute();
	}
}
