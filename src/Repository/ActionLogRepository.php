<?php

namespace App\Repository;

use App\Entity\ActionLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActionLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionLog[]    findAll()
 * @method ActionLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActionLog::class);
    }

    /**
     * @param $orderBy
     * @param $orderDir
     * @param $firstResult
     * @param $maxResults
     * @param $filters
     * @return Query
     * @throws \Exception
     */
    public function standardQuery($orderBy, $orderDir, $firstResult, $maxResults, $filters): Query
    {
        $qb = $this->createQueryBuilder('al');

        if ($filters['Search'] !== null){
            $searchArray = array();
            $whereArray = array();
            foreach (explode(' ', $filters['Search']) as $key => $val){
                $searchArray[$key] = '%' . $val . '%';
                $whereArray[$key] = "(al.action LIKE ?$key OR al.detail LIKE ?$key)";
            }

            $qb
                ->andWhere(implode(' AND ', $whereArray))
                ->setParameters($searchArray);
        }

        if ($filters['User'] !== null){
            $qb
                ->andWhere('al.userCreated = :user')
                ->setParameter('user', $filters['User']);
        }

        if ($filters['DateRange'] !== null) {
            $qb
                ->andWhere('al.dateCreated > :startDate')
                ->setParameter('startDate', $filters['DateRange']);
        }

        if ($filters['EndDate'] !== null) {
            $endDate = new \DateTime($filters['EndDate']);
            $endDate->add(new \DateInterval('P1D'));
            $qb
                ->andWhere('al.dateCreated <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        $qb
            ->orderBy($orderBy, $orderDir)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        return $qb->getQuery();
    }
}