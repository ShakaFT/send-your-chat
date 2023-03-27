<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\Chat\CreateChatDto;
use App\DTO\Chat\JoinChatDto;
use App\Entity\AbstractEntity;
use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

class ChatService extends AbstractEntityService
{

	public function __construct(ChatRepository $chatRepository)
	{
		parent::__construct($chatRepository);
	}

	/**
	 * @param CreateChatDto $dto
	 * @param Chat $entity
	 */
	public function add(AbstractDto $dto, AbstractEntity $entity): string
	{
		$entity->setFromCreateDto($dto);
		return parent::add($dto, $entity);
	}

	/**
	 * @param JoinChatDto $dto
	 * @param Chat $entity
	 */
	public function join(AbstractDto $dto, User $currentUser): string
	{
		$error = "";

		/** @var Chat $chat */
		try {
			$chat = $this->repository->findByToken($dto->token)[0];
		} catch (\Exception) {
			return "Le code d'invitation est invalide.";
		}


		if ($chat->getUsers()->contains($currentUser)) {
			return "Vous faites déjà parti de ce chat.";
		}

		$chat->addUser($currentUser);
		$this->repository->save($chat, true);

		return $error;
	}
}
