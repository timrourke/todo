<?php

namespace App\Tests\App\Serializer;

use App\Entity\Todo;
use App\Entity\TodoId;
use App\Serializer\TodoJsonSerializer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TodoJsonSerializerTest extends TestCase
{
    /**
     * @var int
     */
    private $expectedId;

    /**
     * @var string
     */
    private $expectedTitle;

    /**
     * @var string
     */
    private $expectedDescription;

    /**
     * @var \DateTimeImmutable
     */
    private $expectedCreatedAt;

    /**
     * @var \DateTimeImmutable
     */
    private $expectedUpdatedAt;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();
        $this->expectedId = Uuid::uuid1()->toString();
        $this->expectedTitle = 'Some title';
        $this->expectedDescription = 'Some description';
        $this->expectedCreatedAt = new DateTimeImmutable();
        $this->expectedUpdatedAt = new DateTimeImmutable();
    }

    /**
     * @test
     */
    public function shouldSerializeOneTodo()
    {
        $todo = $this->getTodo();

        $serializer = new TodoJsonSerializer();

        $actual = $serializer->serializeOne($todo);

        $this->assertSame(
            [
                'id' => $this->expectedId,
                'title' => $this->expectedTitle,
                'description' => $this->expectedDescription,
                'createdAt' => $this->expectedCreatedAt->format(DateTimeImmutable::ATOM),
                'updatedAt' => $this->expectedUpdatedAt->format(DateTimeImmutable::ATOM),
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldSerializeManyTodos()
    {
        $todos = [
            $this->getTodo(),
            $this->getTodo(),
            $this->getTodo(),
        ];

        $serializer = new TodoJsonSerializer();

        $actual = $serializer->serializeMany($todos);

        $this->assertCount(3, $todos);

        foreach ($actual as $todo) {
            $this->assertSame(
                [
                    'id' => $this->expectedId,
                    'title' => $this->expectedTitle,
                    'description' => $this->expectedDescription,
                    'createdAt' => $this->expectedCreatedAt->format(DateTimeImmutable::ATOM),
                    'updatedAt' => $this->expectedUpdatedAt->format(DateTimeImmutable::ATOM),
                ],
                $todo
            );
        }
    }

    private function getTodo(): Todo
    {
        return new Todo(
            TodoId::fromUuidString($this->expectedId),
            $this->expectedTitle,
            $this->expectedDescription,
            $this->expectedCreatedAt,
            $this->expectedUpdatedAt
        );
    }
}
