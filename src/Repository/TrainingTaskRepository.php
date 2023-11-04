<?php

namespace App\Repository;

use App\Entity\TrainingTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrainingTask>
 *
 * @method TrainingTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingTask[]    findAll()
 * @method TrainingTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingTask::class);
    }

//    /**
//     * @return TrainingTask[] Returns an array of TrainingTask objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TrainingTask
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
