<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\CreateTodoCommand;
use App\Entity\Todo;
use App\Service\TodoService;
use DateTimeImmutable;

class CreateTodoCommandHandler
{
    /**
     * @var \App\Service\TodoService
     */
    private $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    /**
     * @param \App\Command\CreateTodoCommand $createTodoCommand
     * @throws \Exception
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handle(CreateTodoCommand $createTodoCommand): void
    {
        $newTodo = new Todo(
            $this->todoService->nextIdentity(),
            $createTodoCommand->getTitle(),
            $createTodoCommand->getDescription(),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->todoService->createTodo($newTodo);
    }
}