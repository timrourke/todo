<?php

declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Command\UpdateTodoCommand;
use App\Controller\TodoController;
use App\Entity\Todo;
use App\Entity\TodoId;
use App\JsonApiError\NotFoundError;
use App\JsonApiResponder\JsonApiResponder;
use App\Serializer\TodoJsonSerializer;
use App\Service\TodoService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\App\JsonApiResponder\JsonApiResponder
     */
    private $jsonapiResponderMock;

    protected function setUp()
    {
        parent::setUp();

        $this->todoServiceMock = $this->getMockBuilder(TodoService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandBusMock = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->jsonapiResponderMock = $this->getMockBuilder(JsonApiResponder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new TodoController(
            $this->todoServiceMock,
            $this->commandBusMock,
            $this->jsonapiResponderMock
        );

        $this->serializer = new TodoJsonSerializer();

        $this->controller->setContainer(new Container());
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldGetList()
    {
        $todos = [
            new Todo(
                TodoId::fromUuid(Uuid::uuid1()),
                'Some todo',
                'Some description',
                new DateTimeImmutable(),
                new DateTimeImmutable()
            )
        ];

        $this->todoServiceMock->expects($this->once())
            ->method('findPageOfTodos')
            ->with(0)
            ->willReturn($todos);

        $this->jsonapiResponderMock->expects($this->once())
            ->method('getContentResponse')
            ->with($todos)
            ->willReturn(new Response());

        $this->controller->getList();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldGetOne()
    {
        $uuidString = Uuid::uuid1()->toString();

        $expectedId = TodoId::fromUuidString($uuidString);

        $expectedTodo = new Todo(
            $expectedId,
            'Wash dishes',
            'Gotta wash the dishes!',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->todoServiceMock->expects($this->once())
            ->method('findTodo')
            ->with($uuidString)
            ->willReturn($expectedTodo);

        $this->jsonapiResponderMock->expects($this->once())
            ->method('getContentResponse')
            ->with($expectedTodo)
            ->willReturn(new Response());

        $this->controller->getOne($uuidString);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldRender404WhenTodoDoesNotExist()
    {
        $uuidString = Uuid::uuid1()->toString();

        $this->todoServiceMock->expects($this->once())
            ->method('findTodo')
            ->with($uuidString)
            ->willThrowException(new \RuntimeException());

        $this->jsonapiResponderMock->expects($this->once())
            ->method('getErrorResponse')
            ->with(
                $this->isInstanceOf(NotFoundError::class),
                404
            )
            ->willReturn(new Response());

        $this->controller->getOne($uuidString);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldRender404WhenInvalidUuidProvided()
    {
        $uuidString = Uuid::uuid1()->toString() . "somechars";

        $this->todoServiceMock->expects($this->once())
            ->method('findTodo')
            ->with($uuidString)
            ->willThrowException(new \RuntimeException());

        $this->jsonapiResponderMock->expects($this->once())
            ->method('getErrorResponse')
            ->with(
                $this->isInstanceOf(NotFoundError::class),
                404
            )
            ->willReturn(new Response());

        $this->controller->getOne($uuidString);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldRender404WhenTodoDoesNotExistForUpdate()
    {
        $uuidString = Uuid::uuid1()->toString();

        $expectedTitle = 'Some todo';
        $expectedDescription = 'Some description';

        $this->commandBusMock->expects($this->once())
            ->method('handle')
            ->with(new UpdateTodoCommand(
                TodoId::fromUuidString($uuidString),
                $expectedTitle,
                $expectedDescription
            ))
            ->willThrowException(new \RuntimeException());

        $this->jsonapiResponderMock->expects($this->once())
            ->method('getErrorResponse')
            ->with(
                $this->isInstanceOf(NotFoundError::class),
                404
            )
            ->willReturn(new Response());

        $request = new Request([], [], [], [], [], [], json_encode([
            'data' => [
                'id' => $uuidString,
                'type' => 'todos',
                'attributes' => [
                    'title' => $expectedTitle,
                    'description' => $expectedDescription,
                ],
            ],
        ]));

        $this->controller->update($request, $uuidString);
    }
}
