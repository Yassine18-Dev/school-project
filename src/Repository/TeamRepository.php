<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return Team[]
     */
    public function searchAndSort(string $q, string $sort, string $dir): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($q !== '') {
            $qb->andWhere('LOWER(t.name) LIKE :q')
               ->setParameter('q', '%'.mb_strtolower($q).'%');
        }

        // whitelist côté repo aussi
        $sortMap = [
            'id' => 't.id',
            'name' => 't.name',
        ];
        $sortField = $sortMap[$sort] ?? 't.id';
        $direction = strtolower($dir) === 'desc' ? 'DESC' : 'ASC';

        return $qb->orderBy($sortField, $direction)
                  ->getQuery()
                  ->getResult();
    }
}
