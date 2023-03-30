<?php

namespace App\Repository;

use App\Entity\ServerToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method ServerToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerToken[]    findAll()
 * @method ServerToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerTokenRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerToken::class);
    }
}
