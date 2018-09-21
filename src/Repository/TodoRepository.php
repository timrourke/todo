<?php

namespace App\Repository;

use App\Entity\Todo;
use App\Entity\TodoId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Todo::class);
    }

    /**
     * @return \App\Entity\TodoId
     * @throws \Doctrine\DBAL\DBALException
     */
    public function nextIdentity(): TodoId
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->insert('todo_id', ['id' => null]);

        $lastInsertedId = (int) $conn->lastInsertId();

        return TodoId::fromInteger($lastInsertedId);
    }
}
