<?php

declare(strict_types=1);

namespace App\Tests\App\Deserializer;

use App\Deserializer\TodoJsonDeserializer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TodoJsonDeserializerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDeserializeOneTodo()
    {
        $data = [
            'todo' => [
                'title' => 'An interesting title',
                'description' => 'A clear description of the todo',
                'createdAt' => '2019-01-02T12:34:56Z',
                'updatedAt' => '2019-02-02T12:34:56Z',
            ],
        ];

        $json = json_encode($data);

        $deserializer = new TodoJsonDeserializer();

        $todo = $deserializer->deserializeOne($json);

        $this->assertSame(
            $data['todo']['title'],
            $todo->getTitle()
        );

        $this->assertSame(
            $data['todo']['description'],
            $todo->getDescription()
        );

        $this->assertSame(
            DateTimeImmutable::createFromFormat(
                DateTimeImmutable::ATOM,
                $data['todo']['createdAt']
            )->getTimestamp(),
            $todo->getCreatedAt()->getTimestamp()
        );

        $this->assertSame(
            DateTimeImmutable::createFromFormat(
                DateTimeImmutable::ATOM,
                $data['todo']['updatedAt']
            )->getTimestamp(),
            $todo->getUpdatedAt()->getTimestamp()
        );
    }
}
