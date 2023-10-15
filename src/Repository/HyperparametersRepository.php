<?php

namespace App\Repository;

use App\Entity\Hyperparameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hyperparameters>
 *
 * @method Hyperparameters|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hyperparameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hyperparameters[]    findAll()
 * @method Hyperparameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HyperparametersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hyperparameters::class);
    }

//    /**
//     * @return Hyperparameters[] Returns an array of Hyperparameters objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Hyperparameters
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
