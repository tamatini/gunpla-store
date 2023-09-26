<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
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

    public function findByCategory(string $category):array
    {
        return $this->createQueryBuilder("p")
            ->select('c', 'p')
            ->join("p.category", "c")
            ->where("c.name IN (:category)")
            ->setParameter("category", $category)
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[]
     */
    public function findSearch(SearchData $searchData): array
    {
        return $this->getSearchQuery($searchData)->getQuery()->getResult();
    }

    /**
     * Return min and max price range depending on search query
     * @param SearchData $searchData
     * @return int[]
     */
    public function findMinMax(SearchData $searchData): array
    {
        $query = $this->getSearchQuery($searchData, true)
            ->select('MIN(p.sellPrice) as min', 'MAX(p.sellPrice) as max')
            ->getQuery()
            ->getScalarResult();
        return [(int)$query[0]['min'], (int)$query[0]['max']];
    }

    private function getSearchQuery(SearchData $searchData, $ignorePrice = false): QueryBuilder
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

        if (!empty($searchData->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.sellPrice >= :min')
                ->setParameter('min', $searchData->min);
        }

        if (!empty($searchData->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.sellPrice <= :max')
                ->setParameter('max', $searchData->max);
        }

        return $query;
    }
}
