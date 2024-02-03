<?php

namespace App\Controller\Api;

use App\Repository\TaskRepository;
use App\Request\TaskListRequest;
use App\Request\TaskRequest;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;


class TaskController extends AbstractController
{
    public function list(
        Request $request,
        TaskRepository $repository,
        SerializerInterface $serializer,
        TaskListRequest $taskListRequest
    ): JsonResponse
    {
        $parameters = $request->query->all();
        $validatedData = $taskListRequest->isValid($parameters);
        if ($validatedData && count($validatedData) > 0) {
            $errors = [];
            foreach ($validatedData as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $tasks = $repository->findAllTasksWithPagination($parameters);
        $jsonTasks = $serializer->serialize($tasks, 'json');

        return new JsonResponse($jsonTasks, Response::HTTP_OK, [], true);
    }

    public function create(
        Request $request,
        TaskService $service,
        TaskRequest $taskListRequest
    ): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $validatedData = $taskListRequest->isValid($parameters);
        if ($validatedData && count($validatedData) > 0) {
            $errors = [];
            foreach ($validatedData as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $task = $service->create($parameters);

        return $this->json($task->serialize(), Response::HTTP_CREATED);
    }

    public function update(
        int $id,
        TaskService $service,
        Request $request,
        TaskRequest $taskRequest
    ): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $validatedData = $taskRequest->isValid($parameters);
        if ($validatedData && count($validatedData) > 0) {
            $errors = [];
            foreach ($validatedData as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $task = $service->update(array_merge($parameters, ['id' => $id]));
        if (!$task) {
            return $this->json(['message' => 'Not found']);
        }

        return $this->json($task->serialize(), Response::HTTP_CREATED);
    }

    public function delete(int $id, TaskService $service): JsonResponse
    {
        if ($service->delete($id))
        {
            return $this->json(['message' => 'Task deleted']);
        }

        return $this->json(['message' => 'Not found']);
    }

    public function complete(int $id, TaskService $service): JsonResponse
    {
        if ($service->complete($id))
        {
            return $this->json(['message' => 'Task completed']);
        }

        return $this->json(['message' => 'Not found']);
    }
}
