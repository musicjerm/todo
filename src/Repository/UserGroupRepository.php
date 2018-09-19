<?php

namespace App\Repository;

use App\Entity\UserGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroup[]    findAll()
 * @method UserGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserGroup::class);
    }

    public function standardQuery($orderBy, $orderDir, $firstResult, $maxResults, $filters, $user): Query
    {
        $qb = $this->createQueryBuilder('ug');

        $qb
            ->where('ug.userCreated = :user')
            ->setParameter('user', $user);

        if ($filters['Search'] !== null){
            $qb
                ->andWhere('ug.name LIKE :search')
                ->setParameter('search', "%$filters[Search]%");
        }

        $qb
            ->orderBy($orderBy, $orderDir)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        return $qb->getQuery();
    }
}
