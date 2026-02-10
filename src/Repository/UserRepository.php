<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findPlayersAndCaptains(?string $q = null, string $sort = 'id', string $dir = 'DESC'): array
    {
        $allowedSort = ['id', 'email', 'username', 'roleType', 'status'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'id';
        }
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.roleType IN (:types)')
            ->setParameter('types', ['PLAYER', 'CAPTAIN'])
            ->orderBy('u.' . $sort, $dir);

        if ($q !== null && trim($q) !== '') {
            $qb->andWhere('u.email LIKE :q OR u.username LIKE :q')
               ->setParameter('q', '%' . trim($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findAvailablePlayersAndCaptains(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roleType IN (:types)')
            ->setParameter('types', ['PLAYER', 'CAPTAIN'])
            ->andWhere('u.team IS NULL')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
