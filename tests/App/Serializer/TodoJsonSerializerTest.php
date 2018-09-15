<?php

namespace App\Tests\App\Serializer;

use App\Entity\Todo;
use App\Serializer\TodoJsonSerializer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TodoJsonSerializerTest extends TestCase
{
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

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
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
                'id' => null,
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
                    'id' => null,
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
        $todo = new Todo();
        $todo->setTitle($this->expectedTitle);
        $todo->setDescription($this->expectedDescription);
        $todo->setCreatedAt($this->expectedCreatedAt);
        $todo->setUpdatedAt($this->expectedUpdatedAt);

        return $todo;
    }
}