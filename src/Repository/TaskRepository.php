<?php

namespace App\Repository;

use App\Entity\Task;
use App\Helper\parseRequestParamsHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Поиск задач с постраничным выводом и сортировкой,
     * также возможна фильтрация по статусу (status = 0 или 1).
     * @param HeaderBag $headers
     * @param array $params
     * @return Task[] Returns an array of Task objects
     */
    public function findByPagination(HeaderBag $headers, array $params): array
    {
        $queryBuilder = $this->createQueryBuilder('t');
        if (array_key_exists('status', $params))
        {
            $status = $params['status'];
            if (!is_null($status))
            {
                $queryBuilder->andWhere('t.status = :val')
                    ->setParameter('val', $status);
            }
        }

        $sortingParams = parseRequestParamsHelper::parseSortingParams($params);
        if (empty($sortingParams))
        {
            $queryBuilder->orderBy('t.id', 'DESC');
        } else
        {
            $index = 0;
            foreach ($sortingParams as $key => $value) {
                if ($index = 0) {
                    $queryBuilder->orderBy('t.' . $key, $value);
                } else {
                    $queryBuilder->addOrderBy('t.' . $key, $value);
                }
                $index++;
            }
        }

        $paginationParams = parseRequestParamsHelper::parsePaginationParams($headers, $params);
        if (!empty($paginationParams))
        {
            $queryBuilder->setFirstResult(
                $paginationParams['xPaginationSize'] * ($paginationParams['xPaginationPage'] - 1)
                )
                ->setMaxResults($paginationParams['xPaginationSize']);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}