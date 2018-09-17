<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Task::class);
    }

    // required for JermBundle filters
    public function standardQuery($orderBy, $orderDir, $firstResult, $maxResults, $filters, $user): Query
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.name LIKE :search OR t.description LIKE :search')
            ->setParameter('search', "%$filters[Search]%")
            ->orderBy($orderBy, $orderDir)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        return $qb->getQuery();
    }
}