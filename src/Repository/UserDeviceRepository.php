<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserDevice|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDevice|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDevice[]    findAll()
 * @method UserDevice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDeviceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserDevice::class);
    }

//    /**
//     * @return UserDevice[] Returns an array of UserDevice objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserDevice
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
