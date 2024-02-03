<?php

namespace App\Repository;

use App\Entity\Task;
use App\Service\TaskService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    private const PAGE = 1;

    private const LIMIT = 5;

    private PaginatorInterface $paginator;

    private TaskService $service;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator, TaskService $service)
    {
        parent::__construct($registry, Task::class);
        $this->paginator = $paginator;
        $this->service = $service;
    }

    public function findAllTasksWithPagination(array $parameters): array
    {
        $page = $parameters['page'] ?? self::PAGE;
        $limit = $parameters['limit'] ?? self::LIMIT;

        $queryBuilder = $this->createQueryBuilder('t')->getQuery();

        $pagination = $this->paginator->paginate(
            $queryBuilder,
            $page,
            $limit
        );

        $perPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalItems = $pagination->getTotalItemCount();

        $items = $pagination->getItems();

        if (!empty($items)) {
            $this->service->updateAllViewsCount($items);
        }

        return [
            'items' => $items,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'totalItems' => $totalItems,
        ];
    }
}
