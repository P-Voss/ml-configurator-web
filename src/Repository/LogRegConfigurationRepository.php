<?php

namespace App\Repository;

use App\Entity\LogRegConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogRegConfiguration>
 *
 * @method LogRegConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogRegConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogRegConfiguration[]    findAll()
 * @method LogRegConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRegConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogRegConfiguration::class);
    }

//    /**
//     * @return LogRegConfiguration[] Returns an array of LogRegConfiguration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogRegConfiguration
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
