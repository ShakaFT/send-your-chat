<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\DTO\UserDto;
use App\DTO\ResetPasswordDto;
use App\Entity\AbstractEntity;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserService extends AbstractEntityService {

	private UserPasswordHasherInterface $passwordHasher;

	public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher) {
		parent::__construct($userRepository);
		$this->passwordHasher = $passwordHasher;
	}

	public function getByUsername(string $username): User {
		return $this->repository->findByUsername($username)[0];
	}

	/**
	 * @param UserDto $dto
	 * @param User $entity
	 */
	public function add(AbstractDto $dto, AbstractEntity $entity): string {
		$userWithSameEmail = $this->repository->findByEmail($dto->email);
		$userWithSameUsername = $this->repository->findByUsername($dto->username);
		// set avatar to random image bases on username
		$entity->setAvatar('https://ui-avatars.com/api/?name=' . $dto->username);
		$entity->setDeleted(false);

		if ($userWithSameEmail) {
			return 'Il y a déjà un utilisateur avec cette adresse mail';
		}
		if ($userWithSameUsername) {
			return 'Il y a déjà un utilisateur avec ce pseudo';
		}
		if ($dto->password) {
			if ($dto->password !== $dto->passwordConfirm){
				return 'Les deux mots de passe doivent être identiques';
			}
			$dto->password = $this->encodePassword($entity, $dto->password);
		}
		return parent::add($dto, $entity);
	}

	/**
	 * @param User $entity
	 */
	public function delete(AbstractEntity $entity): void {
		$entity->setDeleted(true);

		// Removing the user from all their servers
		foreach ($entity->getServers() as $server) {
			$entity->removeServer($server);
        }
		$this->repository->save($entity, true);
	}

	/**
	 * @param UserDto $dto
	 * @param User $entity
	 */
	public function update(AbstractDto $dto, AbstractEntity $entity): string {
		$userWithNewMail = $this->repository->findByEmail($dto->email);
		$userWithNewUsername = $this->repository->findByUsername($dto->username);

		if ($userWithNewMail && $userWithNewMail[0]->getId() !== $entity->getId()) {
			return 'Il y a déjà un utilisateur avec cette adresse mail';
		}

		if ($userWithNewUsername && $userWithNewUsername[0]->getId() !== $entity->getId()) {
			return 'Il y a déjà un utilisateur avec ce pseudo';
		}

		return parent::update($dto, $entity);
	}

	/**
	 * @param ResetPasswordDto $dto
	 * @param User $entity
	 */
	public function updatePassword(AbstractDto $dto, AbstractEntity $entity): string {
		if($dto->newPassword !== $dto->confirmPassword) {
			return 'Les deux mots de passe doivent être identiques';
		}
		
		$entity->setPassword($this->encodePassword($entity, $dto->newPassword));
		$this->repository->save($entity, true);
		return '';
		}

	public function encodePassword(PasswordAuthenticatedUserInterface $user, string $value): string {
		return $this->passwordHasher->hashPassword($user, $value);
	}
}
