<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
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
     * @throws NonUniqueResultException
     */
    public function findBySlug($slug): ?Product
    {
        return $this->createQueryBuilder("p")
            ->where("p.slug = :slug")
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Product[] Returns an array of latest products
     */
    public function findLatestProduct(?int $maxResult = 5): array
    {
        return $this->createQueryBuilder("p")
            ->orderBy("p.createdAt", "DESC")
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[]
     */
    public function findSearch(SearchData $searchData): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');

        if (!empty($searchData->search)) {
            $query = $query
                -> andWhere('p.reference LIKE :search')
                ->setParameter('search', "%{$searchData->search}%");
        }

        if (!empty($searchData->categories)) {
            $query = $query
                ->andWhere('p.category IN (:categories)')
                ->setParameter('categories', $searchData->categories);
        }
        return $query->getQuery()->getResult();
    }
}
