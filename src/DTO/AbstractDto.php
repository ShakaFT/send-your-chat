<?php

namespace App\DTO;

use App\Entity\AbstractEntity;

abstract class AbstractDto {

	abstract public function setFromEntity(AbstractEntity $entity): void;
}
