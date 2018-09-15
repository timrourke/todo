<?php

namespace App\Controller;

use App\Deserializer\TodoJsonDeserializer;
use App\Serializer\TodoJsonSerializer;
use App\Service\TodoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @var \App\Deserializer\TodoJsonDeserializer
     */
    private $deserializer;

    public function __construct(
        TodoService $todoService,
        TodoJsonSerializer $serializer,
        TodoJsonDeserializer $deserializer
    ) {
        $this->todoService = $todoService;
        $this->serializer = $serializer;
        $this->deserializer = $deserializer;
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $json = $request->getContent();
        $todo = $this->deserializer->deserializeOne($json);
        $todo = $this->todoService->createTodo($todo);

        return $this->json([
            'todo' => $this->serializer->serializeOne($todo),
        ]);
    }

    /**
     * @Route("/todos/{id}", methods={"PUT"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, int $id)
    {
        $json = $request->getContent();
        $todo = $this->deserializer->deserializeOne($json);
        $updatedTodo = $this->todoService->updateTodo($id, $todo);

        return $this->json([
            'todo' => $this->serializer->serializeOne($updatedTodo),
        ]);
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
