<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    /**
     * @param Category $category
     * @return array
     */
    public function findByCategory(Category $category)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin("p.categories", "c")
            ->where('c = :category')
            ->setParameter('category', $category)
            ->getQuery();

        return $qb->getResult();
    }
}
