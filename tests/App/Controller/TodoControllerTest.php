<?php

declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\TodoController;
use App\Deserializer\TodoJsonDeserializer;
use App\Entity\Todo;
use App\Serializer\TodoJsonSerializer;
use App\Service\TodoService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class TodoControllerTest extends TestCase
{
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

        $this->controller = new TodoController(
            $this->todoServiceMock,
            new TodoJsonSerializer(),
            new TodoJsonDeserializer()
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
     */
    public function shouldGetOne()
    {
        $expectedId = 1;

        $this->todoServiceMock->expects($this->once())
            ->method('findTodo')
            ->with($expectedId)
            ->willReturn(new Todo());

        $actual = $this->controller->getOne($expectedId);

        $this->assertSame(
            json_encode([
                'todo' => $this->serializer->serializeOne(new Todo()),
            ]),
            $actual->getContent()
        );
    }
}
