<?php

namespace App\Repository;

use App\Entity\DecisiontreeConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DecisiontreeConfiguration>
 *
 * @method DecisiontreeConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method DecisiontreeConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method DecisiontreeConfiguration[]    findAll()
 * @method DecisiontreeConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DecisiontreeConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DecisiontreeConfiguration::class);
    }

//    /**
//     * @return DecisiontreeConfiguration[] Returns an array of DecisiontreeConfiguration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DecisiontreeConfiguration
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
