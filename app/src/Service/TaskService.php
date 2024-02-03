<?php

namespace App\Service;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(array $data): Task
    {
        $task = new Task($data);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    public function delete(int $id): bool
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if ($task) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function complete(int $id): Task|bool
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if ($task) {
            $task->setStatus(Task::COMPLETED);
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $task;
        }

        return false;
    }

    public function update(array $params): Task|bool
    {
        $task = $this->entityManager->getRepository(Task::class)->find($params['id']);
        if ($task) {
            $task->setText($params['text']);
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $task;
        }

        return false;
    }

    public function updateAllViewsCount(array $items): void
    {
        $taskIds = [];
        foreach ($items as $item) {
            $taskIds[] = $item->getId();
        }
        $this->entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->update()
            ->set('t.views_count', 't.views_count + 1')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $taskIds)
            ->getQuery()
            ->execute();

//        Цей запит можна буде використати якщо буде потрібно оновити поле status залежно від умов:
//        Статус:
//          - новая (только создана)
//          - просмотренная (та что хоть раз просмотрели)
//          - важная (задача которой уже больше 1 дня)
//          - выполненная (та что уже закрылась)

//            ->createQueryBuilder('t')
//            ->update()
//            ->set('t.views_count', 't.views_count + 1')
//            ->set('t.status', 'CASE WHEN t.status != :completedStatus THEN CASE WHEN t.updated_at < :oneDayAgo THEN :importantStatus ELSE :viewedStatus END ELSE t.status END')
//            ->where('t.id IN (:ids)')
//            ->setParameter('ids', $taskIds)
//            ->setParameter('oneDayAgo', new \DateTime('-1 day'))
//            ->setParameter('completedStatus', Task::COMPLETED)
//            ->setParameter('importantStatus', Task::IMPORTANT)
//            ->setParameter('viewedStatus', Task::VIEWED)
//            ->getQuery()
//            ->execute();
    }
}