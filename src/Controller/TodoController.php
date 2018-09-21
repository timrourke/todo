<?php

declare(strict_types=1);

namespace App\Controller;

use App\Command\CreateTodoCommand;
use App\Command\UpdateTodoCommand;
use App\Deserializer\TodoJsonDeserializer;
use App\Entity\TodoId;
use App\Serializer\TodoJsonSerializer;
use App\Service\TodoService;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function getList()
    {
        $todos = $this->todoService->findPageOfTodos(0);

        return $this->json([
            'todos' => $this->serializer->serializeMany($todos),
        ]);
    }

    /**
     * @Route("/todos/{id}", methods={"GET"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOne(int $id)
    {
        $todo = $this->todoService->findTodo($id);

        return $this->json([
            'todo' => $this->serializer->serializeOne($todo),
        ]);
    }

    /**
     * @Route("/todos", methods={"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $command = new CreateTodoCommand(
            $this->todoService->nextIdentity(),
            $data['todo']['title'],
            $data['todo']['description']
        );

        $this->commandBus->handle($command);

        $response = new Response();
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * @Route("/todos/{id}", methods={"PUT"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function update(Request $request, int $id)
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $command = new UpdateTodoCommand(
            TodoId::fromInteger($id),
            $data['todo']['title'],
            $data['todo']['description']
        );

        $this->commandBus->handle($command);

        $response = new Response();
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/todos/{id}", methods={"DELETE"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(int $id)
    {
        $this->todoService->deleteTodo($id);

        return $this->json(new \stdClass());
    }
}
