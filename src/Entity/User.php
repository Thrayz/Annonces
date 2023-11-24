<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Email = null;

    #[ORM\Column(length: 5000, nullable: true)]
    private ?string $Password = null;

    #[ORM\Column(nullable: true)]
    private ?int $UserType = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Annonce::class)]
    private Collection $Annonces;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $Comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Category::class)]
    private Collection $Categories;

    public function __construct()
    {
        $this->Annonces = new ArrayCollection();
        $this->Categories = new ArrayCollection();
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(?string $Username): static
    {
        $this->Username = $Username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(?string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(?string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getUserType(): ?int
    {
        return $this->UserType;
    }

    public function setUserType(?int $UserType): static
    {
        $this->UserType = $UserType;

        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->Annonces;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->Annonces->contains($annonce)) {
            $this->Annonces->add($annonce);
            $annonce->setUser($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        if ($this->Annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->Categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->Categories->contains($category)) {
            $this->Categories->add($category);
            $category->setUser($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->Categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getUser() === $this) {
                $category->setUser(null);
            }
        }

        return $this;
    }
}
