<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\UpdateTodoCommand;
use App\Entity\Todo;
use App\Service\TodoService;
use DateTimeImmutable;

class UpdateTodoCommandHandler
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
     * @param \App\Command\UpdateTodoCommand $updateTodoCommand
     * @throws \Exception
     */
    public function handle(UpdateTodoCommand $updateTodoCommand): void
    {
        $updatedTodo = new Todo(
            $updateTodoCommand->getId(),
            $updateTodoCommand->getTitle(),
            $updateTodoCommand->getDescription(),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->todoService->updateTodo($updatedTodo);
    }

}