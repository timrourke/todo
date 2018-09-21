<?php

declare(strict_types=1);

namespace App\Tests\App\CommandHandler;

use App\Command\CreateTodoCommand;
use App\CommandHandler\CreateTodoCommandHandler;
use App\Entity\Todo;
use App\Entity\TodoId;
use App\Service\TodoService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateTodoCommandHandlerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testHandle()
    {
        $command = $this->createCommand();

        /* @var \PHPUnit\Framework\MockObject\MockObject|\App\Service\TodoService $serviceMock */
        $serviceMock = $this->getMockBuilder(TodoService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceMock->expects($this->once())
            ->method('createTodo')
            ->willReturnCallback(function (Todo $todo) use ($command) {
                $this->assertSame(
                    $command->getId()->asString(),
                    $todo->getId()->asString()
                );

                $this->assertSame(
                    $command->getTitle(),
                    $todo->getTitle()
                );

                $this->assertSame(
                    $command->getDescription(),
                    $todo->getDescription()
                );

                $this->assertInstanceOf(
                    DateTimeImmutable::class,
                    $todo->getCreatedAt()
                );

                $this->assertInstanceOf(
                    DateTimeImmutable::class,
                    $todo->getUpdatedAt()
                );
            });

        $handler = new CreateTodoCommandHandler($serviceMock);

        $handler->handle($command);
    }

    private function createCommand(): CreateTodoCommand
    {
        $uuidString = Uuid::uuid1()->toString();

        return new CreateTodoCommand(
            TodoId::fromUuidString($uuidString),
            '',
            ''
        );
    }
}
