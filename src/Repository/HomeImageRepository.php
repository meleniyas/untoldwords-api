<?php

namespace App\Repository;

use App\Entity\HomeImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HomeImage>
 *
 * @method HomeImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method HomeImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method HomeImage[]    findAll()
 * @method HomeImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HomeImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeImage::class);
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
