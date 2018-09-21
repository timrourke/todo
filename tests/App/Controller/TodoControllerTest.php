<?php

declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\TodoController;
use App\Entity\Todo;
use App\Entity\TodoId;
use App\Serializer\TodoJsonSerializer;
use App\Service\TodoService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Symfony\Component\DependencyInjection\Container;

class TodoControllerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CommandBus
     */
    private $commandBusMock;

    /**
     * @var TodoController
     */
    private $controller;

    /**
     * @var \App\Serializer\TodoJsonSerializer
     */
    private $serializer;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|TodoService
     */
    private $todoServiceMock;

    protected function setUp()
    {
        parent::setUp();

        $this->todoServiceMock = $this->getMockBuilder(TodoService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandBusMock = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new TodoController(
            $this->todoServiceMock,
            new TodoJsonSerializer(),
            $this->commandBusMock
        );

        $this->serializer = new TodoJsonSerializer();

        $this->controller->setContainer(new Container());
    }

    /**
     * @test
     */
    public function shouldGetList()
    {
        $this->todoServiceMock->expects($this->once())
            ->method('findPageOfTodos')
            ->with(0)
            ->willReturn([]);

        $actual = $this->controller->getList();

        $this->assertSame(
            json_encode([
                'todos' => [],
            ]),
            $actual->getContent()
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldGetOne()
    {
        $expectedId = 1;

        $expectedTodo = new Todo(
            TodoId::fromInteger(1),
            'Wash dishes',
            'Gotta wash the dishes!',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->todoServiceMock->expects($this->once())
            ->method('findTodo')
            ->with($expectedId)
            ->willReturn($expectedTodo);

        $actual = $this->controller->getOne($expectedId);

        $this->assertSame(
            json_encode([
                'todo' => $this->serializer->serializeOne($expectedTodo),
            ]),
            $actual->getContent()
        );
    }
}
