<?php

namespace App\Tests\App\JsonApiResponder;

use App\Entity\Todo;
use App\Entity\TodoId;
use App\JsonApiResponder\JsonApiResponder;
use App\JsonApiSchema\TodoSchema;
use DateTimeImmutable;
use Neomerx\JsonApi\Factories\Factory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class JsonApiResponderTest extends TestCase
{
    /**
     * @var \Neomerx\JsonApi\Factories\Factory
     */
    private $factory;

    /**
     * @var \App\JsonApiResponder\JsonApiResponder
     */
    private $responder;

    protected function setUp()
    {
        parent::setUp();

        $this->factory = new Factory();

        $this->responder = new JsonApiResponder($this->factory, [
            Todo::class => TodoSchema::class,
        ]);
    }

    public function testGetNoContentResponse()
    {
        $actual = $this->responder->getNoContentResponse();

        $this->assertSame(
            204,
            $actual->getStatusCode()
        );

        $this->assertSame(
            '',
            $actual->getContent()
        );
    }

    /**
     * @throws \Exception
     */
    public function testGetCreatedResponse()
    {
        $todo = new Todo(
            TodoId::fromUuidString(Uuid::uuid1()),
            'Some title',
            'Some description',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        /* @var \Symfony\Component\HttpFoundation\Response $actual */
        $actual = $this->responder->getCreatedResponse($todo);

        $this->assertSame(
            201,
            $actual->getStatusCode()
        );
    }
}
