<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CategoryName = null;

    #[ORM\ManyToOne(inversedBy: 'Categories')]
    private ?Annonce $annonce = null;

    #[ORM\ManyToOne(inversedBy: 'Categories')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->CategoryName;
    }

    public function setCategoryName(?string $CategoryName): static
    {
        $this->CategoryName = $CategoryName;

        return $this;
    }

    public function getAnnoce(): ?Annonce
    {
        return $this->Annoce;
    }

    public function setAnnoce(?Annonce $Annoce): static
    {
        $this->Annoce = $Annoce;

        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): static
    {
        $this->annonce = $annonce;

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
