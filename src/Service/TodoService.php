<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Todo;
use App\Entity\TodoId;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

/**
 * Class TodoService
 * @package App\Service
 */
class TodoService
{
    public const FIND_MANY_LIMIT = 20;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Repository\TodoRepository
     */
    private $repo;

    public function __construct(
        EntityManagerInterface $entityManager,
        TodoRepository $repo
    ) {
        $this->entityManager = $entityManager;
        $this->repo = $repo;
    }

    /**
     * @param int $id
     * @return \App\Entity\Todo
     */
    public function findTodo(int $id): Todo
    {
        return $this->repo->find($id);
    }

    /**
     * @param int $offset
     * @return array
     */
    public function findPageOfTodos(int $offset): array
    {
        return $this->repo->findBy(
            [],
            ['createdAt' => 'DESC'],
            self::FIND_MANY_LIMIT,
            $offset
        );
    }

    /**
     * @param \App\Entity\Todo $newTodo
     * @throws \Exception
     */
    public function createTodo(Todo $newTodo): void
    {
        $this->entityManager->persist($newTodo);
        $this->entityManager->flush();
    }

    /**
     * @param \App\Entity\Todo $todoWithNewData
     * @throws \Exception
     */
    public function updateTodo(Todo $todoWithNewData): void
    {
        $todo = $this->repo->find($todoWithNewData->getId()->asInt());

        if (!$todo) {
            throw new RuntimeException(
                sprintf(
                    'No Todo found by ID %d',
                    $todoWithNewData->getId()->asInt()
                )
            );
        }

        $todo->changeTitle($todoWithNewData->getTitle());
        $todo->changeDescription($todoWithNewData->getDescription());

        $this->entityManager->persist($todo);
        $this->entityManager->flush();
    }

    /**
     * @param int $id
     */
    public function deleteTodo(int $id): void
    {
        $todo = $this->repo->find($id);

        // Deleting a non-existing entity should be idempotent
        if (is_null($todo)) {
            return;
        }

        $this->entityManager->remove($todo);
        $this->entityManager->flush();
    }

    /**
     * @return \App\Entity\TodoId
     * @throws \Doctrine\DBAL\DBALException
     */
    public function nextIdentity(): TodoId
    {
        return $this->repo->nextIdentity();
    }
}