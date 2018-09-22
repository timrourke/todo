<?php

declare(strict_types=1);

namespace App\Tests\App\JsonApiSchema;

use App\Entity\Todo;
use App\Entity\TodoId;
use App\JsonApiSchema\TodoSchema;
use DateTimeImmutable;
use Neomerx\JsonApi\Factories\Factory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TodoSchemaTest extends TestCase
{

    public function testGetId()
    {
        $todo = $this->getTodo();

        $schema = new TodoSchema(new Factory());

        $actual = $schema->getId($todo);

        $this->assertSame(
            $todo->getId()->asString(),
            $actual
        );
    }

    public function testGetAttributes()
    {
        $todo = $this->getTodo();

        $schema = new TodoSchema(new Factory());

        $actual = $schema->getAttributes($todo);

        $this->assertSame(
            [
                'title'       => $todo->getTitle(),
                'description' => $todo->getDescription(),
                'created-at'  => $todo->getCreatedAt()->format(DateTimeImmutable::ATOM),
                'updated-at'  => $todo->getUpdatedAt()->format(DateTimeImmutable::ATOM),
            ],
            $actual
        );

    }

    private function getTodo(): Todo
    {
        return new Todo(
            TodoId::fromUuid(Uuid::uuid1()),
            'Some todo',
            'Some description',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    }
}
