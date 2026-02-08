<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
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
        minMessage: "Le nom du tournoi doit comporter au moins {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Veuillez sélectionner une date pour le tournoi.")]
    #[Assert\GreaterThan(
        "now",
        message: "La date du tournoi doit être dans le futur."
    )]
    private ?\DateTimeInterface $dateTournoi = null;

    #[ORM\ManyToOne(targetEntity: Jeu::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Vous devez impérativement choisir un jeu.")]
    private ?Jeu $jeu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateTournoi(): ?\DateTimeInterface
    {
        return $this->dateTournoi;
    }

    public function setDateTournoi(\DateTimeInterface $dateTournoi): static
    {
        $this->dateTournoi = $dateTournoi;

        return $this;
    }

    public function getJeu(): ?Jeu
    {
        return $this->jeu;
    }

    public function setJeu(?Jeu $jeu): static
    {
        $this->jeu = $jeu;

        return $this;
    }
}