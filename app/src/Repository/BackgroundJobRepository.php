<?php

namespace App\Repository;

use App\Entity\BackgroundJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BackgroundJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackgroundJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackgroundJob[]    findAll()
 * @method BackgroundJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackgroundJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackgroundJob::class);
    }

    public function findAllByPage(
        int $page,
        int $itemsPerPage
    ) {
        $offset = ($page - 1) * $itemsPerPage;

        $query = $this->createQueryBuilder('j')
            ->orderBy('j.lastExecution', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($itemsPerPage);

        return new Paginator($query, true);
    }
}
