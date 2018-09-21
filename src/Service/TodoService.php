<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Todo;
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
     * @param string $id
     * @return \App\Entity\Todo
     * @throws \RuntimeException
     */
    public function findTodo(string $id): Todo
    {
        $todo = $this->repo->find($id);

        if (!$todo) {
            $this->throwNoTodoFoundException($id);
        }

        return $todo;
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
        $todo = $this->repo->find($todoWithNewData->getId()->asString());

        if (!$todo) {
            $this->throwNoTodoFoundException(
                $todoWithNewData->getId()->asString()
            );
        }

        $todo->changeTitle($todoWithNewData->getTitle());
        $todo->changeDescription($todoWithNewData->getDescription());

        $this->entityManager->persist($todo);
        $this->entityManager->flush();
    }

    /**
     * @param string $id
     */
    public function deleteTodo(string $id): void
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
     * @param string $id
     * @throws \RuntimeException
     */
    private function throwNoTodoFoundException(string $id)
    {
        throw new RuntimeException(
            sprintf('No Todo found by ID %d', $id)
        );
    }
}