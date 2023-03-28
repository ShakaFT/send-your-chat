<?php

namespace App\Repository;

use App\Entity\DiscussionMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method DiscussionMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscussionMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscussionMessage[]    findAll()
 * @method DiscussionMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionMessageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscussionMessage::class);
    }
}
