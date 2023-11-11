<?php

namespace App\Repository;

use App\Entity\FieldConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FieldConfiguration>
 *
 * @method FieldConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method FieldConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method FieldConfiguration[]    findAll()
 * @method FieldConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldConfiguration::class);
    }

//    /**
//     * @return FieldConfiguration[] Returns an array of FieldConfiguration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FieldConfiguration
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
