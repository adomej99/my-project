<?php

namespace App\Repository;

use App\Entity\MainBookImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainBookImage>
 *
 * @method MainBookImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainBookImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainBookImage[]    findAll()
 * @method MainBookImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainBookImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainBookImage::class);
    }

    public function save(MainBookImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MainBookImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMainByTitleOrAuthor(string $searchQuery)
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.title LIKE :searchQuery OR b.author LIKE :searchQuery')
            ->setParameter('searchQuery', "%{$searchQuery}%");

        return $qb->getQuery()->getResult();
    }
}
