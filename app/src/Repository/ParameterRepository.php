<?php

namespace App\Repository;

use App\Entity\Parameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameter[]    findAll()
 * @method Parameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    public function findAllByPage(
        int $page,
        int $itemsPerPage
    ) {
        $offset = ($page - 1) * $itemsPerPage;

        $query = $this->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($itemsPerPage);

        return new Paginator($query, true);
    }

    public function findByName(string $name)
    {
        try {
            return $this->createQueryBuilder('p')
                ->where('p.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findParametersByNames(array $names)
    {
        return $this->findBy(['name' => $names]);
    }
}
