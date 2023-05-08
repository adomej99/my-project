<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAvailableNotOwner(int $id, ?string $isbn)
    {

        $qb = $this->createQueryBuilder('u');
        if($isbn)
        {
            $qb->where('u.owner != :identifier')
                ->andwhere('u.available = :status')
                ->andwhere('u.isbn = :isbn')
                ->setParameters(array('status' => 1, 'identifier' => $id, 'isbn' => $isbn));
        }
        else
        {
            $qb->where('u.owner != :identifier')
                ->andwhere('u.available = :status')
                ->andwhere('u.isbn is NULL')
                ->setParameters(array('status' => 1, 'identifier' => $id));
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function searchByTitleAndAuthor($searchQuery, $userId)
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.owner = :userId')
            ->setParameter('userId', $userId);

        if (!empty($searchQuery)) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('b.Title', ':search'),
                $qb->expr()->like('b.Author', ':search')
            ))->setParameter('search', "%{$searchQuery}%");
        }

        return $qb->getQuery()->getResult();
    }
}
