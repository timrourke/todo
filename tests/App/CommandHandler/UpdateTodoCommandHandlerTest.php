<?php

declare(strict_types=1);

namespace App\Tests\App\CommandHandler;

use App\Command\UpdateTodoCommand;
use App\CommandHandler\UpdateTodoCommandHandler;
use App\Entity\Todo;
use App\Entity\TodoId;
use App\Service\TodoService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateTodoCommandHandlerTest extends TestCase
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
            ->method('updateTodo')
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

        $handler = new UpdateTodoCommandHandler($serviceMock);

        $handler->handle($command);
    }

    private function createCommand(): UpdateTodoCommand
    {
        $uuidString = Uuid::uuid1()->toString();

        return new UpdateTodoCommand(
            TodoId::fromUuidString($uuidString),
            '',
            ''
        );
    }
}
