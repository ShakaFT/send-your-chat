<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\Server\ChangeServerNameDto;
use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\Entity\AbstractEntity;
use App\Entity\Server;
use App\Entity\User;
use App\Repository\ServerRepository;
use DateTimeImmutable;

class ServerService extends AbstractEntityService
{

	public function __construct(ServerRepository $serverRepository)
	{
		parent::__construct($serverRepository);
	}

	/**
	 * @param CreateServerDto $dto
	 * @param Server $entity
	 */
	public function add(AbstractDto $dto, AbstractEntity $entity): string
	{
		$now = new DateTimeImmutable();
		$entity->setLastInteraction($now->getTimestamp());
		$entity->setFromCreateDto($dto);
		return parent::add($dto, $entity);
	}

	/**
	 * @param JoinServerDto $dto
	 * @param Server $entity
	 */
	public function join(AbstractDto $dto, User $currentUser): string
	{
		$error = "";

		/** @var Server $server */
		try {
			$server = $this->repository->findByToken($dto->token)[0];
		} catch (\Exception) {
			return "Le code d'invitation est invalide.";
		}


		if ($server->getUsers()->contains($currentUser)) {
			return "Vous faites déjà parti de ce serveur.";
		}

		$server->addUser($currentUser);
		$this->repository->save($server, true);

		return $error;
	}

	public function refreshLastInteraction(int $serverId) {
		$now = new DateTimeImmutable();

		$server = $this->getById($serverId);
		$server->setLastInteraction($now->getTimestamp());
		$this->repository->save($server, true);
	}

	/**
	 * @param ChangeServerNameDto $dto
	 */
	public function changeName(int $serverId, AbstractDto $dto) {
		$server = $this->getById($serverId);
		if ($server->getName() === $dto->serverName) {
			return 'Le serveur posséde déjà ce nom.';
		}

		$server->setName($dto->serverName);
		$this->repository->save($server, true);

		return '';
	}

	public function getById(int $id) : Server
	{
		return $this->repository->findById($id)[0];
	}
}
