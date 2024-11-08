<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TaskController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepository,
        private SerializerInterface $serializer,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/task', name: 'app_task', methods: ['GET'])]
    public function index(#[MapQueryParameter()] ?int $status = null)
    {
        $criteria = [];
        if (!is_null($status)) {
            $criteria['status'] = $status;
        }
        $tasks = $this->taskRepository->findBy($criteria, ['id' => 'DESC']);
        $response = new JsonResponse($this->serializer->serialize($tasks, 'json'), 200, [], true);
        return $response;
    }

    #[Route('/task', name: 'add_task', methods: ['POST'])]
    public function addTask( #[MapRequestPayload()] Task $task)
    {
        $this->em->persist($task);
        $this->em->flush();
        $response = new JsonResponse($this->serializer->serialize($task, 'json'), 200, [], true);
        return $response;
    }

    #[Route('/task/{id}', name: 'edit_task', methods: ['PUT'])]
    public function editTask(int $id, #[MapRequestPayload()] Task $data)
    {
        /**
         * @var Task
         */
        $task = $this->taskRepository->find($id);
        if (!$task) {
            throw new NotFoundHttpException('Задание с id = ' . $id . ' не найдено!');
        };
        $task->setName($data->getName());
        $task->setDescription($data->getDescription());
        $task->setEndDate($data->getEndDate());
        $task->setStatus($data->getStatus());
        $this->em->flush();
        $response = new JsonResponse($this->serializer->serialize($task, 'json'), 200, [], true);
        return $response;
    }

    #[Route('/task/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(int $id)
    {
        /**
         * @var Task
         */
        $task = $this->taskRepository->find($id);
        if (!$task) {
            throw new NotFoundHttpException('Задание с id = ' . $id . ' не найдено!');
        };
        $this->em->remove($task);
        $this->em->flush();
        $response = new JsonResponse([]);
        return $response;
    }
}
