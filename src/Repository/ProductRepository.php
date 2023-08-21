<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 //* @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param $slug
     * @return Product|null Returns a single product depending on slug
     */
    public function findBySlug($slug) : ?Product
    {
        return $this->createQueryBuilder("p")
            ->from("Product", "p")
            ->where("p.slug = :slug")
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of containing all products
     * @throws Exception
     */
    public function findAll() : array {
        $request = "SELECT * FROM product";
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($request);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }

    /**
     * @return Product[] Returns an array of latest products
     * @throws Exception
     */
    public function findLatestProduct() : array
    {
        $request = "SELECT * FROM product ORDER BY created_at DESC LIMIT 5";
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($request);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }
}
