<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


   public function findWithCategoryAndImage(): array
   {
        return $this->createQueryBuilder('p')
           ->leftJoin('p.category', 'c')
           ->addSelect('c')
           ->leftJoin('p.images', 'i', 'with', 'i.isPrincipal = true')
           ->addSelect('i')
           ->getQuery()
           ->getResult()
       ;
   }

   public function findWithCategory(): array
   {
        return $this->createQueryBuilder('p')
           ->leftJoin('p.category', 'c')
           ->addSelect('c')
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneWithCategoryAndImage(int $id = 0): ?Product
   { 
        return ($id) ? $this->createQueryBuilder('p')
           ->leftJoin('p.category', 'c')
           ->addSelect('c')
           ->leftJoin('p.images', 'i')
           ->addSelect('i')
           ->where('p.id = :id')
           ->setParameter('id', $id)
           ->getQuery()
           ->getOneOrNullResult()
        : null
        ;
   }


//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
