<?php

namespace App\Repository;

use App\Entity\Tournoi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tournoi>
 */
class TournoiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournoi::class);
    }

    /**
     * Recherche, filtre par jeu, et trie les tournois.
     */
    public function searchAndFilter(?string $search = null, ?int $jeuId = null, string $sort = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.jeu', 'j')
            ->addSelect('j');

        // Recherche par nom
        if ($search) {
            $qb->andWhere('t.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Filtre par jeu
        if ($jeuId) {
            $qb->andWhere('j.id = :jeuId')
               ->setParameter('jeuId', $jeuId);
        }

        // Tri par date
        $qb->orderBy('t.dateDebut', $sort === 'DESC' ? 'DESC' : 'ASC');

        return $qb->getQuery()->getResult();
    }
}
