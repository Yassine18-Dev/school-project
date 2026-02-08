<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\DBAL\Types\Types;
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
    #[Assert\NotBlank(message: "La prédiction doit être liée à un tournoi.")]
    private ?Tournoi $tournoi = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du vainqueur prédit ne peut pas être vide.")]
    private ?string $vainqueurPredi = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 1, notInRangeMessage: "La confiance doit être entre 0 et 1.")]
    private ?float $confianceAI = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        // Initialise la date automatiquement à la création
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournoi(): ?Tournoi
    {
        return $this->tournoi;
    }

    public function setTournoi(?Tournoi $tournoi): static
    {
        $this->tournoi = $tournoi;

        return $this;
    }

    public function getVainqueurPredi(): ?string
    {
        return $this->vainqueurPredi;
    }

    public function setVainqueurPredi(string $vainqueurPredi): static
    {
        $this->vainqueurPredi = $vainqueurPredi;

        return $this;
    }

    public function getConfianceAI(): ?float
    {
        return $this->confianceAI;
    }

    public function setConfianceAI(float $confianceAI): static
    {
        $this->confianceAI = $confianceAI;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}