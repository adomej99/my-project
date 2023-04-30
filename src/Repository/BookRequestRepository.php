<?php

namespace App\Repository;

use App\Entity\BookRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookRequest>
 *
 * @method BookRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookRequest[]    findAll()
 * @method BookRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookRequest::class);
    }

    public function save(BookRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BookRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getBookRequests(int $id)
    {
        $qb = $this->createQueryBuilder('br');

        $qb
            ->select('br', 'b')
            ->leftJoin('br.book', 'b')
            ->where('b.owner = :userId')
            ->andWhere('br.isActive = :isActive')
            ->andWhere('br.isLent = :isLent')
            ->setParameters(['userId' => $id, 'isActive' => 1, 'isLent' => 0]);

        return $qb->getQuery()
            ->getResult();
    }

    public function getMyLentBooks($id)
    {
        $qb = $this->createQueryBuilder('br');

        $qb
            ->select('br', 'b')
            ->leftJoin('br.book', 'b')
            ->where('b.owner = :userId')
            ->andWhere('br.isLent = :isLent')
            ->andWhere('br.isActive = :isActive')
            ->setParameters(['userId' => $id, 'isActive' => 0, 'isLent' => 1]);

        return $qb->getQuery()
            ->getResult();
    }
}
