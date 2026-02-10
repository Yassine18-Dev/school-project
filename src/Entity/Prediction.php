<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
class Prediction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tournoi::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournoi $tournoi = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'équipe A est obligatoire.")]
    private ?string $teamA = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'équipe B est obligatoire.")]
    private ?string $teamB = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $scoreTeamA = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $scoreTeamB = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le vainqueur prédit est obligatoire.")]
    private ?string $predictedWinner = null;

    #[ORM\Column(type: "float")]
    #[Assert\Range(min: 0, max: 1)]
    private ?float $winProbability = 0.5;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $aiAnalysis = null;

    public function getId(): ?int { return $this->id; }

    public function getTournoi(): ?Tournoi { return $this->tournoi; }
    public function setTournoi(?Tournoi $tournoi): self { $this->tournoi = $tournoi; return $this; }

    public function getTeamA(): ?string { return $this->teamA; }
    public function setTeamA(string $teamA): self { $this->teamA = $teamA; return $this; }

    public function getTeamB(): ?string { return $this->teamB; }
    public function setTeamB(string $teamB): self { $this->teamB = $teamB; return $this; }

    public function getScoreTeamA(): ?int { return $this->scoreTeamA; }
    public function setScoreTeamA(?int $scoreTeamA): self { $this->scoreTeamA = $scoreTeamA; return $this; }

    public function getScoreTeamB(): ?int { return $this->scoreTeamB; }
    public function setScoreTeamB(?int $scoreTeamB): self { $this->scoreTeamB = $scoreTeamB; return $this; }

    public function getPredictedWinner(): ?string { return $this->predictedWinner; }
    public function setPredictedWinner(string $predictedWinner): self { $this->predictedWinner = $predictedWinner; return $this; }

    public function getWinProbability(): ?float { return $this->winProbability; }
    public function setWinProbability(float $winProbability): self { $this->winProbability = $winProbability; return $this; }

    public function getAiAnalysis(): ?string { return $this->aiAnalysis; }
    public function setAiAnalysis(?string $aiAnalysis): self { $this->aiAnalysis = $aiAnalysis; return $this; }
}