<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\Server\ChangeServerNameDto;
use App\DTO\Server\CreateServerDto;
use App\DTO\Server\JoinServerDto;
use App\DTO\Server\UpdateServerOwnerDto;
use App\Entity\AbstractEntity;
use App\Entity\Server;
use App\Entity\User;
use App\Repository\ServerRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

class ServerService extends AbstractEntityService
{

	private ServerMessageService $serverMessageService;
	private UserRepository $userRepository;

	public function __construct(ServerRepository $serverRepository, ServerMessageService $serverMessageService, UserRepository $userRepository)
	{
		$this->serverMessageService = $serverMessageService;
		$this->userRepository = $userRepository;
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
	public function changeName(int $serverId, AbstractDto $dto) : string {
		$server = $this->getById($serverId);
		if ($server->getName() === $dto->serverName) {
			return 'Le serveur posséde déjà ce nom.';
		}

		$server->setName($dto->serverName);
		$this->repository->save($server, true);

		return '';
	}

		/**
	 * @param UpdateServerOwnerDto $dto
	 */
	public function changeOwner(int $serverId, AbstractDto $dto) : string {
		$server = $this->getById($serverId);
		if ($server->getOwner()->getUsername() === $dto->newOwner) {
			return 'Vous êtes déjà le propriétaire.';
		}
		$newOwner = $this->userRepository->findByUsername($dto->newOwner);

		if (!$newOwner) {
			return "L'utilisateur n'existe pas";
		}

		if (!$server->getUsers()->contains($newOwner[0])) {
			return "L'utilisateur ne fait pas parti du serveur";
		}

		$server->setOwner($newOwner[0]);
		$this->repository->save($server, true);

		return '';
	}

	/**
	 * @param Server $server
	 */
	public function delete(AbstractEntity $server): void {
		$this->serverMessageService->deleteMessages($server);
		parent::delete($server);
	}

	public function getById(int $id) : Server
	{
		return $this->repository->findById($id)[0];
	}

	/**
     * @return Collection<int, User>
     */
	public function getMembers(int $serverId) : Collection
	{
		return $this->getById($serverId)->getUsers();
	}
}
