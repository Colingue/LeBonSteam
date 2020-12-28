<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Query\AST\Join;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Recherche les posts avec le formulaire
     *
     */
    public function findPostByName(string $query,  $category) {
        $qb = $this->createQueryBuilder('p');
        $qb->innerJoin('p.category_id', 'c');

        if (is_array($category) || is_object($category))
        {
        foreach ($category as $field => $value) {
            if (!$this->getClassMetadata()->hasField($field)) {
                // Make sure we only use existing fields (avoid any injection)
                continue;
            }
        }

        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('p.title', ':query'),
                        $qb->expr()->like('p.description', ':query'),
                        $qb->expr()->eq('p.'.$field, ':p_'.$field)
                    ),
                    $qb->expr()->isNotNull('p.date_creation')

                )
            )
            ->setParameter('query', '%'.$query.'%')
            ->setParameter('p_'.$field, $value);
        return $qb
            ->getQuery()
            ->getResult();
    }}

    /**
     * Recupere les posts liés a la recherche
     * @return Post[]
     */
    public function findSearch(SearchData $search): array {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');

            if (!empty($search->q)) {
                $query = $query
                    ->andWhere('p.title LIKE :q')
                    ->setParameter('q', "%{$search->q}%");
            }

            if (!empty($search->category)) {
                $query = $query
                    ->andWhere('c.id IN (:category)')
                    ->setParameter('category', $search->category);
            }
        return $query->getQuery()->getResult();
    }



    public function apiFindAll() {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id', 'a.title', 'a.description', 'a.category', 'a.dateCreation')
            ->orderBy('a.dateCreation', 'DESC');

        $query = $qb->getQuery();

        return $query->execute();
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
