<?php

namespace App\Entity;

use App\Repository\SupportCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportCategoryRepository::class)]
class SupportCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, SupportRequest>
     */
    #[ORM\OneToMany(targetEntity: SupportRequest::class, mappedBy: 'category')]
    private Collection $supportRequests;

    public function __construct()
    {
        $this->supportRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, SupportRequest>
     */
    public function getSupportRequests(): Collection
    {
        return $this->supportRequests;
    }

    public function addSupportRequest(SupportRequest $supportRequest): static
    {
        if (!$this->supportRequests->contains($supportRequest)) {
            $this->supportRequests->add($supportRequest);
            $supportRequest->setCategory($this);
        }

        return $this;
    }

    public function removeSupportRequest(SupportRequest $supportRequest): static
    {
        if ($this->supportRequests->removeElement($supportRequest)) {
            // set the owning side to null (unless already changed)
            if ($supportRequest->getCategory() === $this) {
                $supportRequest->setCategory(null);
            }
        }

        return $this;
    }
}
