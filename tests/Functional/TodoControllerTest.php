<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Todo;
use App\Entity\TodoId;
use App\Serializer\TodoJsonSerializer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoControllerTest extends WebTestCase
{
    use BootstrapsSqliteTestDatabaseTrait;

    /**
     * @var \App\Serializer\TodoJsonSerializer
     */
    private $serializer;

    protected function setUp()
    {
        parent::setUp();

        self::bootKernel();

        $this->bootstrapSqliteTestDatabase(self::$kernel);

        $this->serializer = new TodoJsonSerializer();
    }

    /**
     * @test
     * @throws \Doctrine\DBAL\DBALException
     */
    public function shouldGetOneTodo()
    {
        $expectedId = 8;
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
              {$expectedId},
              '{$expectedTitle}',
              '{$expectedDescription}',
              '{$expectedCreatedAt->format('Y-m-d H:i:s')}',
              '{$expectedUpdatedAt->format('Y-m-d H:i:s')}'
            )
        ");

        $client = static::createClient();

        $client->request(
            'GET',
            '/todos/' . $expectedId
        );

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(
            [
                'todo' => [
                    'id' => $expectedId,
                    'title' => $expectedTitle,
                    'description' => $expectedDescription,
                    'createdAt' => $expectedCreatedAt->format(DateTimeImmutable::ATOM),
                    'updatedAt' => $expectedUpdatedAt->format(DateTimeImmutable::ATOM),
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
            $this->connection->exec("
              INSERT INTO todo
              (
                title,
                description,
                created_at,
                updated_at
              )
              VALUES
              (
                'Some todo',
                'Some description',
                '2012-01-02 09:12:45',
                '2018-09-01 09:12:45'
              )
            ");
        }

        $client = static::createClient();

        $client->request(
            'GET',
            '/todos'
        );

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertCount(
            20,
            $json['todos']
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldCreateTodo()
    {
        $todo = new Todo(
            TodoId::fromInteger(1),
            'Some title',
            'Some description',
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $client = static::createClient();

        $payload = ['todo' => $this->serializer->serializeOne($todo)];

        $client->request(
            'POST',
            '/todos',
            [],
            [],
            [],
            json_encode($payload)
        );

        $this->assertSame(201, $client->getResponse()->getStatusCode());

        $todos = $this->connection->fetchAll(
            'SELECT id, title, description FROM todo'
        );

        $this->assertSame(
            [
                [
                    'id' => $this->connection->fetchColumn('SELECT MAX(id) FROM todo_id'),
                    'title' => 'Some title',
                    'description' => 'Some description',
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
        $requestData = [
            'todo' => [
                'id' => 1,
                'title' => 'New title',
                'description' => 'New description',
                'createdAt' => '2012-01-12T07:34:09Z',
                'updatedAt' => '2012-01-12T07:34:09Z',
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
              1,
              'Some title',
              'Some description',
              '2012-01-12 07:34:09',
              '2012-01-12 07:34:09'
            )
        ");

        $client = static::createClient();

        $client->request(
            'PUT',
            '/todos/1',
            [],
            [],
            [],
            json_encode($requestData)
        );

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $updatedTodo = $this->connection->fetchAll('SELECT * FROM todo WHERE id = 1');

        $this->assertSame(
            $requestData['todo']['id'],
            (int) $updatedTodo[0]['id']
        );

        $this->assertSame(
            $requestData['todo']['title'],
            $updatedTodo[0]['title']
        );

        $this->assertSame(
            $requestData['todo']['description'],
            $updatedTodo[0]['description']
        );

        $this->assertSame(
            DateTimeImmutable::createFromFormat(
                DateTimeImmutable::ATOM,
                $requestData['todo']['createdAt']
            )->getTimestamp(),
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $updatedTodo[0]['created_at']
            )->getTimestamp()
        );

        $this->assertGreaterThan(
            DateTimeImmutable::createFromFormat(
                DateTimeImmutable::ATOM,
                $requestData['todo']['updatedAt']
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
              2,
              'A title',
              'A description',
              '2015-01-01 08:12:15',
              '2015-01-01 08:12:15'
            )
        ");

        $client = static::createClient();

        $client->request(
            'DELETE',
            '/todos/2'
        );

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame('{}', $response->getContent());

        $foundTodos = $this->connection->fetchAll('SELECT * FROM todo WHERE id = 2');

        $this->assertSame([], $foundTodos);
    }

    /**
     * @test
     */
    public function shouldNotThrowErrorWhenDeletingNonExistentTodo()
    {
        $client = static::createClient();

        $client->request('DELETE', '/todos/4362346');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
