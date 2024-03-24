<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5000, nullable: true)]
    private ?string $Content = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\ManyToOne]
    private ?User $users = null;

    #[ORM\ManyToOne]
    private ?Annonce $annonces = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(?string $Content): static
    {
        $this->Content = $Content;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(?\DateTimeInterface $Date): static
    {
        $this->Date = $Date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->users;
    }

    public function setUser(?User $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonces;
    }

    public function setAnnonce(?Annonce $annonces): static
    {
        $this->annonces = $annonces;

        return $this;
    }
    /**
     * @param int $userId
     * @return comment[]
     */
    public function findCommentsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.comments', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

}
