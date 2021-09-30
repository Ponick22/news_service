<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('publication_date' => 'ASC'));
    }

    public function countAll($isVisible = false)
    {
        $qb = $this->createQueryBuilder('n');
        if($isVisible)
            $qb->where('n.isVisible = true');
        return $qb->select('count(n.id)')                    
                    ->getQuery()
                    ->getSingleScalarResult();;                        
    }

    public function limit($start = 0, $limit, $isVisible = false)
    {
        $qb = $this->createQueryBuilder('n');
        if($isVisible)
            $qb->where('n.isVisible = true');
        return $qb->orderBy('n.publication_date', 'ASC')
                    ->setMaxResults($limit)
                    ->setFirstResult($start)
                    ->getQuery()
                    ->getResult();                        
    }
    // /**
    //  * @return News[] Returns an array of News objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?News
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
