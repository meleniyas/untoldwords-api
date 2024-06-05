<?php

namespace App\Repository;

use App\Entity\WorkImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkImage>
 *
 * @method WorkImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkImage[]    findAll()
 * @method WorkImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkImage::class);
    }

    //    /**
    //     * @return Admin[] Returns an array of Admin objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Admin
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
