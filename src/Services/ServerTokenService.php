<?php

namespace App\Services;

use App\Entity\Server;
use App\Repository\ServerTokenRepository;

class ServerTokenService extends AbstractEntityService
{

	public function __construct(ServerTokenRepository $serverTokenRepository)
	{
		parent::__construct($serverTokenRepository);
	}


    public function deleteTokens(Server $server) {
		$this->repository->createQueryBuilder('token')
			->delete()
			->where('token.server = :server_id')
			->setParameter('server_id', $server->getId())
			->getQuery()
			->execute();
	}

}