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
            ->orderBy($orderBy, $orderDir)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        if (!\in_array('ROLE_ADMIN', $user->getRoles(), true)){
            $qb
                ->leftJoin('t.userSubscribed', 'us')
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

        if ($filters['Status'] !== null){
            $qb
                ->andWhere('t.status IN (:status)')
                ->setParameter('status', $filters['Status']);
        }

        if ($filters['Priority'] !== null){
            $qb
                ->andWhere('t.priority = :priority')
                ->setParameter('priority', $filters['Priority']);
        }

        if ($filters['Tag_Category'] !== null){
            $qb
                ->andWhere('t.tags LIKE :tag')
                ->setParameter('tag', "%$filters[Tag_Category]%");
        }

        return $qb->getQuery();
    }

    public function findTags(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.tags')
            ->groupBy('t.tags');

        if (!\in_array('ROLE_ADMIN', $user->getRoles(), true)){
            $qb
                ->leftJoin('t.userSubscribed', 'us')
                ->andWhere('(t.userCreated = :user OR us = :user OR t.public = :public)')
                ->setParameters(['user' => $user, 'public' => true]);
        }

        $array = array();
        foreach ($qb->getQuery()->getArrayResult() as $tags){
            foreach ($tags['tags'] as $tag){
                \in_array(['tag' => $tag], $array, true) ?: $array[] = ['tag' => $tag];
            }
        }

        return $array;
    }
}