<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Todo;
use App\Entity\TodoId;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoControllerTest extends WebTestCase
{
    use BootstrapsSqliteTestDatabaseTrait;

    protected function setUp()
    {
        parent::setUp();

        self::bootKernel();

        $this->bootstrapSqliteTestDatabase(self::$kernel);
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function shouldGetOneTodo()
    {
        $uuid = Uuid::uuid1();
        $expectedId = $uuid->toString();
        $expectedTitle = 'Neat todo';
        $expectedDescription = 'This todo is neat';
        $expectedCreatedAt = DateTimeImmutable::createFromFormat(
            DateTimeImmutable::ATOM,
            '2012-01-02T12:13:14Z'
        );
        $expectedUpdatedAt = DateTimeImmutable::createFromFormat(
            DateTimeImmutable::ATOM,
            '2012-02-02T12:13:14Z'
        );

        $this->connection->exec("
            INSERT INTO todo
            (
              id,
              title,
              description,
              created_at,
              updated_at
            )
            VALUES
            (
              '{$expectedId}',
              '{$expectedTitle}',
              '{$expectedDescription}',
              '{$expectedCreatedAt->format('Y-m-d H:i:s')}',
              '{$expectedUpdatedAt->format('Y-m-d H:i:s')}'
            )
        ");

        $client = static::createClient();

        $client->request(
            'GET',
            '/api/todos/' . $uuid->toString()
        );

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(
            [
                'data' => [
                    'type' => 'todos',
                    'id' => $expectedId,
                    'attributes' => [
                        'title' => $expectedTitle,
                        'description' => $expectedDescription,
                        'created-at' => $expectedCreatedAt->format(DateTimeImmutable::ATOM),
                        'updated-at' => $expectedUpdatedAt->format(DateTimeImmutable::ATOM),
                    ],
                    'links' => [
                        'self' => '/todos/' . $expectedId,
                    ],
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function shouldGetManyTodos()
    {
        for ($i = 0; $i < 20; $i++) {
            $uuidString = Uuid::uuid1()->toString();

            $this->connection->exec("
              INSERT INTO todo
              (
                id,
                title,
                description,
                created_at,
                updated_at
              )
              VALUES
              (
                '$uuidString',
                'Some todo',
                'Some description',
                '2012-01-02 09:12:45',
                '2018-09-01 09:12:45'
              )
            ");
        }

        $client = static::createClient();

        $client->request('GET', '/api/todos');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertCount(
            20,
            $json['data']
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldCreateTodo()
    {
        $expectedId = TodoId::fromUuid(Uuid::uuid1());
        $expectedTitle = 'Some title';
        $expectedDescription = 'Some description';

        $todo = new Todo(
            $expectedId,
            $expectedTitle,
            $expectedDescription,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $client = static::createClient();

        $payload = [
            'data' => [
                'type' => 'todos',
                'id' => $expectedId->asString(),
                'attributes' => [
                    'title' => $expectedTitle,
                    'description' => $expectedDescription,
                ]
            ]
        ];

        $client->request(
            'POST',
            '/api/todos',
            [],
            [],
            [],
            json_encode($payload)
        );

        $this->assertSame(204, $client->getResponse()->getStatusCode());

        $todos = $this->connection->fetchAll(
            'SELECT id, title, description FROM todo'
        );

        $this->assertSame(
            [
                [
                    'id' => $todo->getId()->asString(),
                    'title' => $expectedTitle,
                    'description' => $expectedDescription,
                ],
            ],
            $todos
        );
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function shouldUpdateExistingTodo()
    {
        $uuidString = Uuid::uuid1()->toString();

        $requestData = [
            'data' => [
                'id' => $uuidString,
                'type' => 'todos',
                'attributes' => [
                    'title' => 'New title',
                    'description' => 'New description',
                    'created-at' => '2012-01-12T07:34:09Z',
                    'updated-at' => '2012-01-12T07:34:09Z',
                ],
            ],
        ];

        $this->connection->exec("
            INSERT INTO todo
            (
              id,
              title,
              description,
              created_at,
              updated_at
            )
            VALUES
            (
              '$uuidString',
              'Some title',
              'Some description',
              '2012-01-12 07:34:09',
              '2012-01-12 07:34:09'
            )
        ");

        $client = static::createClient();

        $client->request(
            'PATCH',
            '/api/todos/' . $uuidString,
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $updatedTodo = $this->connection
            ->fetchAll("SELECT * FROM todo WHERE id = '$uuidString'");

        $this->assertSame(
            $requestData['data']['id'],
            $updatedTodo[0]['id']
        );

        $this->assertSame(
            $requestData['data']['attributes']['title'],
            $updatedTodo[0]['title']
        );

        $this->assertSame(
            $requestData['data']['attributes']['description'],
            $updatedTodo[0]['description']
        );

        $this->assertSame(
            DateTimeImmutable::createFromFormat(
                DateTimeImmutable::ATOM,
                $requestData['data']['attributes']['created-at']
            )->getTimestamp(),
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $updatedTodo[0]['created_at']
            )->getTimestamp()
        );

        $this->assertGreaterThan(
            DateTimeImmutable::createFromFormat(
                DateTimeImmutable::ATOM,
                $requestData['data']['attributes']['updated-at']
            )->getTimestamp(),
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $updatedTodo[0]['updated_at']
            )->getTimestamp()
        );
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function shouldDeleteTodo()
    {
        $uuidString = Uuid::uuid1()->toString();

        $this->connection->exec("
            INSERT INTO todo
            (
              id,
              title,
              description,
              created_at,
              updated_at
            )
            VALUES (
              '$uuidString',
              'A title',
              'A description',
              '2015-01-01 08:12:15',
              '2015-01-01 08:12:15'
            )
        ");

        $client = static::createClient();

        $client->request(
            'DELETE',
            '/api/todos/' . $uuidString
        );

        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

        $foundTodos = $this->connection
            ->fetchAll("SELECT * FROM todo WHERE id = '$uuidString'");

        $this->assertSame([], $foundTodos);
    }

    /**
     * @test
     */
    public function shouldNotThrowErrorWhenDeletingNonExistentTodo()
    {
        $uuidString = Uuid::uuid1()->toString();

        $client = static::createClient();

        $client->request('DELETE', '/api/todos/' . $uuidString);

        $this->assertSame(204, $client->getResponse()->getStatusCode());
    }
}
