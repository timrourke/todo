<?php

declare(strict_types=1);

namespace App\Controller;

use App\Command\CreateTodoCommand;
use App\Command\UpdateTodoCommand;
use App\Entity\TodoId;
use App\JsonApiError\NotFoundError;
use App\JsonApiResponder\JsonApiResponder;
use App\Service\TodoService;
use Doctrine\DBAL\Types\ConversionException;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TodoController
 * @package App\Controller
 * @Route("/api/todos", name="todo_")
 */
class TodoController extends AbstractController
{
    /**
     * @var \App\Service\TodoService
     */
    private $todoService;

    /**
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;

    /**
     * @var \App\JsonApiResponder\JsonApiResponder
     */
    private $responder;

    public function __construct(
        TodoService $todoService,
        CommandBus $commandBus,
        JsonApiResponder $responder
    ) {
        $this->todoService = $todoService;
        $this->commandBus = $commandBus;
        $this->responder = $responder;
    }

    /**
     * @Route("/", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getList(): Response
    {
        $todos = $this->todoService->findPageOfTodos(0);

        return $this->responder->getContentResponse($todos);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getOne(string $id): Response
    {
        try {
            $todo = $this->todoService->findTodo($id);

            return $this->responder->getContentResponse($todo);
        } catch (RuntimeException | InvalidUuidStringException | ConversionException $e) {
            return $this->responder->getErrorResponse(
                NotFoundError::createForType('Todo', $id),
                404
            );
        }
    }

    /**
     * @Route("/", methods={"POST"})
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

        return $this->responder->getNoContentResponse();
    }

    /**
     * @Route("/{id}", methods={"PATCH"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function update(Request $request, string $id): Response
    {
        try {
            $json = $request->getContent();
            $data = json_decode($json, true);

            $command = new UpdateTodoCommand(
                TodoId::fromUuidString($id),
                $data['data']['attributes']['title'],
                $data['data']['attributes']['description']
            );

            $this->commandBus->handle($command);

            return $this->responder->getNoContentResponse();
        } catch (RuntimeException | InvalidUuidStringException | ConversionException $e) {
            return $this->responder->getErrorResponse(
                NotFoundError::createForType('Todo', $id),
                404
            );
        }
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(string $id): Response
    {
        $this->todoService->deleteTodo($id);

        return $this->responder->getNoContentResponse();
    }
}
