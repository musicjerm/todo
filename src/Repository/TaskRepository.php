<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
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
    public function standardQuery($orderBy, $orderDir, $firstResult, $maxResults, $filters, User $user): Query
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->leftJoin('t.userSubscribed', 'us')
            ->orderBy($orderBy, $orderDir)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        if (!\in_array('ROLE_ADMIN', $user->getRoles(), true)){
            $qb
                ->andWhere('(t.userCreated = :user OR us = :user OR t.public = :public)')
                ->setParameters(['user' => $user, 'public' => true]);
        }

        if ($filters['Search'] !== null){
            $whereArray = array();
            foreach (explode(' ', $filters['Search']) as $key => $val){
                $whereArray[$key] = "(t.title LIKE ?$key OR t.description LIKE ?$key)";
                $qb->setParameter($key, "%$val%");
            }

            $qb->andWhere(implode(' AND ', $whereArray));
        }

        return $qb->getQuery();
    }
}