<?php

namespace App\Repository;

use App\Entity\Jednostka;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Jednostka|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jednostka|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jednostka[]    findAll()
 * @method Jednostka[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JednostkaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Jednostka::class);
    }

//    /**
//     * @return Jednostka[] Returns an array of Jednostka objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Jednostka
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
