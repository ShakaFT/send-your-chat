<?php

namespace App\Services;

use App\DTO\AbstractDto;
use App\Entity\AbstractEntity;
use App\Repository\AbstractRepository;

abstract class AbstractEntityService {

	protected AbstractRepository $repository;

	public function __construct(AbstractRepository $repository) {
		$this->repository = $repository;
	}

	public function add(AbstractDto $dto, AbstractEntity $entity): string {
		$entity->setFromDto($dto);
		$this->repository->save($entity, true);
		return "";
	}

	public function update(AbstractDto $dto, AbstractEntity $entity): string {
		$entity->setFromDto($dto);
		$this->repository->save($entity, true);
		return "";
	}

	public function delete(AbstractEntity $entity): string {
		$this->repository->remove($entity, true);
		return '';
	}
}
