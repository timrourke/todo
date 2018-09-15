<?php

namespace App\Tests\App\Services;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use App\Service\TodoService;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

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
     */
    public function shouldFindTodo()
    {
        $expectedId = 1;

        $todo = new Todo();

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
     */
    public function shouldFindPageOfTodos()
    {
        $expectedOffset = 8;
        $expectedArgs = [
            [],
            ['createdAt' => 'DESC'],
            TodoService::FIND_MANY_LIMIT,
            $expectedOffset
        ];

        $todos = [
            new Todo()
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
        $expectedTitle = 'Important task';
        $expectedDescription = 'Need to do something important!';

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $todo = $service->createTodo($expectedTitle, $expectedDescription);

        $this->assertSame(
            $expectedTitle,
            $todo->getTitle()
        );

        $this->assertSame(
            $expectedDescription,
            $todo->getDescription()
        );

        $this->assertGreaterThanOrEqual(
            (new DateTimeImmutable())->getTimestamp(),
            $todo->getCreatedAt()->getTimestamp()
        );

        $this->assertGreaterThanOrEqual(
            (new DateTimeImmutable())->getTimestamp(),
            $todo->getUpdatedAt()->getTimestamp()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldPersistCreatedTodo()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Todo::class));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $service->createTodo('Some title', 'Some description');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldUpdateTodo()
    {
        $newTitle = 'New title';
        $newDescription = 'New description';
        $originalCreatedAt = (new DateTimeImmutable())->sub(new DateInterval('P1D'));
        $originalUpdatedAt = (new DateTimeImmutable())->sub(new DateInterval('P1D'));

        $todo = new Todo();

        $todo->setTitle('Original title');
        $todo->setDescription('Original description');
        $todo->setCreatedAt($originalCreatedAt);
        $todo->setUpdatedAt($originalUpdatedAt);

        $this->repoMock->expects($this->once())
            ->method('find')
            ->willReturn($todo);

        $service = new TodoService($this->entityManagerMock, $this->repoMock);

        $service->updateTodo(
            1,
            $newTitle,
            $newDescription
        );

        $this->assertSame(
            $newTitle,
            $todo->getTitle()
        );

        $this->assertSame(
            $newDescription,
            $todo->getDescription()
        );

        $this->assertSame(
            $originalCreatedAt->getTimestamp(),
            $todo->getCreatedAt()->getTimestamp()
        );

        $this->assertGreaterThan(
            $originalUpdatedAt->getTimestamp(),
            $todo->getUpdatedAt()->getTimestamp()
        );
    }

    /**
     * @test
     */
    public function shouldDeleteTodo()
    {
        $expectedId = 1;

        $todo = new Todo();

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
