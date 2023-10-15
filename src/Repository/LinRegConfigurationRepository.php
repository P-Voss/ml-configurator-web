<?php

namespace App\Repository;

use App\Entity\LinRegConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinRegConfiguration>
 *
 * @method LinRegConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinRegConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinRegConfiguration[]    findAll()
 * @method LinRegConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinRegConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinRegConfiguration::class);
    }

//    /**
//     * @return LinRegConfiguration[] Returns an array of LinRegConfiguration objects
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

//    public function findOneBySomeField($value): ?LinRegConfiguration
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
