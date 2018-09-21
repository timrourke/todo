<?php

namespace App\Tests\App\Services;

use App\Entity\Todo;
use App\Entity\TodoId;
use App\Repository\TodoRepository;
use App\Service\TodoService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TodoServiceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EntityManager
     */
    private $entityManagerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|TodoRepository
     */
    private $repoMock;

    protected function setUp()
    {
        parent::setUp();

        $this->entityManagerMock = $this->getEntityManagerMock();
        $this->repoMock = $this->getRepoMock();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldFindTodo()
    {
        $expectedId = Uuid::uuid1()->toString();

        $todo = new Todo(
            TodoId::fromUuidString($expectedId),
            'Clean windows',
            'The windows are super dirty',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->repoMock->expects($this->once())
            ->method('find')
            ->with($expectedId)
            ->willReturn($todo);

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $foundTodo = $service->findTodo($expectedId);

        $this->assertSame(
            $todo,
            $foundTodo
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldFindPageOfTodos()
    {
        $expectedId = Uuid::uuid1()->toString();
        $expectedOffset = 8;
        $expectedArgs = [
            [],
            ['createdAt' => 'DESC'],
            TodoService::FIND_MANY_LIMIT,
            $expectedOffset
        ];

        $todos = [
            new Todo(
                TodoId::fromUuidString($expectedId),
                'Get more socks',
                'Gotta wear socks',
                new DateTimeImmutable(),
                new DateTimeImmutable()
            ),
        ];

        $this->repoMock->expects($this->once())
            ->method('findBy')
            ->with(...$expectedArgs)
            ->willReturn($todos);

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $foundTodos = $service->findPageOfTodos($expectedOffset);

        $this->assertSame(
            $todos,
            $foundTodos
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldCreateTodo()
    {
        $expectedId = Uuid::uuid1()->toString();

        $newTodo = new Todo(
            TodoId::fromUuidString($expectedId),
            'Clean car',
            'Good lord this ride is filthy',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($newTodo);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $service->createTodo($newTodo);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldUpdateTodo()
    {
        $expectedId = Uuid::uuid1()->toString();

        $originalTodo = new Todo(
            TodoId::fromUuidString($expectedId),
            'Original Title',
            'Some description',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $todoWithUpdatedData = new Todo(
            TodoId::fromUuidString($expectedId),
            'New title',
            'New description',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->repoMock->expects($this->once())
            ->method('find')
            ->with($originalTodo->getId()->asString())
            ->willReturn($originalTodo);

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($originalTodo);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $service->updateTodo($todoWithUpdatedData);

        $this->assertSame(
            $todoWithUpdatedData->getTitle(),
            $originalTodo->getTitle()
        );

        $this->assertSame(
            $todoWithUpdatedData->getDescription(),
            $originalTodo->getDescription()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldThrowIfUpdatedTodoDoesNotExist()
    {
        $expectedId = Uuid::uuid1()->toString();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'No Todo found by ID %d',
                $expectedId
            )
        );

        $todo = new Todo(
            TodoId::fromUuidString($expectedId),
            'Some Title',
            'Some description',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->repoMock->expects($this->once())
            ->method('find')
            ->with($todo->getId()->asString())
            ->willReturn(null);

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $service->updateTodo($todo);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldDeleteTodo()
    {
        $expectedId = Uuid::uuid1()->toString();

        $todo = new Todo(
            TodoId::fromUuidString($expectedId),
            'Some todo',
            'Some description',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->repoMock->expects($this->once())
            ->method('find')
            ->with($expectedId)
            ->willReturn($todo);

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($todo);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $service->deleteTodo($expectedId);
    }

    /**
     * @test
     */
    public function shouldDoNothingIfDeletedTodoDoesNotExist()
    {
        $expectedId = Uuid::uuid1()->toString();

        $this->repoMock->expects($this->once())
            ->method('find')
            ->with($expectedId)
            ->willReturn(null);

        $this->entityManagerMock->expects($this->never())
            ->method('remove');

        $this->entityManagerMock->expects($this->never())
            ->method('flush');

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $service->deleteTodo($expectedId);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|EntityManager
     */
    private function getEntityManagerMock()
    {
        return $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|TodoRepository
     */
    private function getRepoMock()
    {
        return $this->getMockBuilder(TodoRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
