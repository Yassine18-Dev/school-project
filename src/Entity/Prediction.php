<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
class Prediction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $scorePredit = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datePrediction = null;

    #[ORM\ManyToOne(targetEntity: Tournoi::class, inversedBy: 'predictions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournoi $tournoi = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        // La date de prédiction est fixée automatiquement à l'instant T de la création
        $this->datePrediction = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScorePredit(): ?string
    {
        return $this->scorePredit;
    }

    public function setScorePredit(string $scorePredit): static
    {
        $this->scorePredit = $scorePredit;
        return $this;
    }

    public function getDatePrediction(): ?\DateTimeImmutable
    {
        return $this->datePrediction;
    }

    // Pas de setDatePrediction car elle est gérée par le constructeur

    public function getTournoi(): ?Tournoi
    {
        return $this->tournoi;
    }

    public function setTournoi(?Tournoi $tournoi): static
    {
        $this->tournoi = $tournoi;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}