<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\ORM\Mapping as ORM;
// Ne pas oublier cet import pour les validations
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du tournoi ne peut pas être vide.")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Le nom doit faire au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull(message: "La date est obligatoire.")]
    #[Assert\Type("\DateTimeInterface")]
    // --- LA RÈGLE POUR LE PASSÉ ---
    #[Assert\GreaterThan(
        value: "today",
        message: "La date du tournoi doit être fixée dans le futur (à partir de demain)."
    )]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(targetEntity: Jeu::class, inversedBy: 'tournois')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Veuillez sélectionner un jeu pour ce tournoi.")]
    private ?Jeu $jeu = null;

    // --- Méthode Métier : Statut dynamique ---
    public function getStatut(): string
    {
        $now = new \DateTime();

        if ($this->dateDebut > $now) {
            return 'À venir';
        }

        if ($this->dateFin !== null && $this->dateFin < $now) {
            return 'Terminé';
        }

        return 'En cours';
    }

    // ... Getters et Setters ...
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(?\DateTimeInterface $dateDebut): self { $this->dateDebut = $dateDebut; return $this; }
    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): self { $this->dateFin = $dateFin; return $this; }
    public function getJeu(): ?Jeu { return $this->jeu; }
    public function setJeu(?Jeu $jeu): self { $this->jeu = $jeu; return $this; }
}