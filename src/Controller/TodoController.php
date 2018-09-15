<?php

namespace App\Controller;

use App\Serializer\TodoJsonSerializer;
use App\Service\TodoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(TodoService $todoService, TodoJsonSerializer $serializer)
    {
        $this->todoService = $todoService;
        $this->serializer = $serializer;
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
     * @throws \Exception
     */
    public function create()
    {
        $todo = $this->todoService->createTodo('foo', 'bar');

        return $this->json([
            'todo' => $this->serializer->serializeOne($todo),
        ]);
    }

    /**
     * @Route("/todos/{id}", methods={"PUT"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function update(int $id)
    {
        $updatedTodo = $this->todoService->updateTodo(
            $id,
            'new title',
            'new description'
        );

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
