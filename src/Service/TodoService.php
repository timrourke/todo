<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

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
     * @param string $title
     * @param string $description
     * @return \App\Entity\Todo
     * @throws \Exception
     */
    public function createTodo(string $title, string $description): Todo
    {
        $todo = new Todo();

        $todo->setTitle($title);
        $todo->setDescription($description);
        $todo->setCreatedAt(new DateTimeImmutable());
        $todo->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        return $todo;
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @return \App\Entity\Todo
     * @throws \Exception
     */
    public function updateTodo(int $id, string $title, string $description): Todo
    {
        $todo = $this->repo->find($id);

        $todo->setTitle($title);
        $todo->setDescription($description);
        $todo->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        return $todo;
    }

    /**
     * @param int $id
     */
    public function deleteTodo(int $id): void
    {
        $todo = $this->repo->find($id);

        $this->entityManager->remove($todo);
        $this->entityManager->flush();
    }
}