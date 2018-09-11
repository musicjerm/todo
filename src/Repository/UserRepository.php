<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * @param string $username
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        $user = $qb->getOneOrNullResult();

        if ($user === null){
            throw new UsernameNotFoundException('Unable to find user');
        }

        return $user;
    }

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function standardQuery($orderBy, $orderDir, $firstResult, $maxResults, $filters)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->addSelect("CONCAT(u.firstName, ' ', u.lastName) AS HIDDEN hFullName");

        $qb
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy($orderBy, $orderDir);

        if ($filters['Search'] !== null){
            $searchArray = array();
            $whereArray = array();
            foreach (explode(' ', $filters['Search']) as $key => $val){
                $searchArray[$key] = '%' . $val . '%';
                $whereArray[$key] = "(u.username LIKE ?$key OR u.email LIKE ?$key OR u.firstName LIKE ?$key OR u.lastName LIKE ?$key)";
            }

            $qb
                ->andWhere(implode(' AND ', $whereArray))
                ->setParameters($searchArray);
        }

        return $qb->getQuery();
    }
}
