<?php

namespace App\Repository;

use App\Entity\AbstractEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;

abstract class AbstractRepository extends ServiceEntityRepository {

	public function save(AbstractEntity $entity, bool $flush = false): void {
		$this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
	}

	public function remove(AbstractEntity $entity, bool $flush = false): void {
		if (!$entity) {
			throw new EntityNotFoundException('Entity not found');
		}

		$this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
	}
}
