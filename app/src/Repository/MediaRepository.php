<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * @param int $page Page number (starting at 1).
     * @param int $size Page size.
     *
     * @return Paginator
     *
     * @psalm-return Paginator<mixed>
     */
    public function findAllByPage(
        int $page,
        int $size
    ): Paginator {
        $offset = ($page - 1) * $size;

        $query = $this->createQueryBuilder('p')
            ->setFirstResult($offset)
            ->setMaxResults($size);

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

    /**
     * @param array $names Array of names requested.
     *
     * @return Media[]
     *
     * @psalm-return array<array-key, Media>
     */
    public function findMediasByNames(array $names): array
    {
        return $this->findBy(['name' => $names]);
    }
}
