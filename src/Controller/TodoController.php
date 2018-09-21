<?php

declare(strict_types=1);

namespace App\Controller;

use App\Command\CreateTodoCommand;
use App\Command\UpdateTodoCommand;
use App\Entity\TodoId;
use App\Serializer\TodoJsonSerializer;
use App\Service\TodoService;
use League\Tactician\CommandBus;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    /**
     * @var \App\Service\TodoService
     */
    private $todoService;

    /**
     * @var \App\Serializer\TodoJsonSerializer
     */
    private $serializer;

    /**
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;

    public function __construct(
        TodoService $todoService,
        TodoJsonSerializer $serializer,
        CommandBus $commandBus
    ) {
        $this->todoService = $todoService;
        $this->serializer = $serializer;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/todos", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getList(): JsonResponse
    {
        $todos = $this->todoService->findPageOfTodos(0);

        return $this->json([
            'todos' => $this->serializer->serializeMany($todos),
        ]);
    }

    /**
     * @Route("/todos/{id}", methods={"GET"})
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOne(string $id): JsonResponse
    {
        try {
            $todo = $this->todoService->findTodo($id);

            return $this->json([
                'todo' => $this->serializer->serializeOne($todo),
            ]);
        } catch (RuntimeException $e) {
            return $this->json(
                [],
                404
            );
        }
    }

    /**
     * @Route("/todos", methods={"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $command = new CreateTodoCommand(
            TodoId::fromUuidString($data['todo']['id']),
            $data['todo']['title'],
            $data['todo']['description']
        );

        $this->commandBus->handle($command);

        return new Response('', 201);
    }

    /**
     * @Route("/todos/{id}", methods={"PUT"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function update(Request $request, string $id): Response
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $command = new UpdateTodoCommand(
            TodoId::fromUuidString($id),
            $data['todo']['title'],
            $data['todo']['description']
        );

        $this->commandBus->handle($command);

        return new Response('', 200);
    }

    /**
     * @Route("/todos/{id}", methods={"DELETE"})
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        $this->todoService->deleteTodo($id);

        return $this->json(new \stdClass());
    }
}
