<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use App\Entity\User;
use App\Entity\TokenPack;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: TokenPack::class)]
    #[ORM\JoinColumn(nullable: false)]
    private TokenPack $tokenPack;

    #[ORM\Column]
    private int $totalCents;

    #[ORM\Column(length: 50)]
    private string $status;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $paidAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getTokenPack(): TokenPack { return $this->tokenPack; }
    public function setTokenPack(TokenPack $tp): static { $this->tokenPack = $tp; return $this; }

    public function getTotalCents(): int { return $this->totalCents; }
    public function setTotalCents(int $c): static { $this->totalCents = $c; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }

    public function getPaidAt(): ?DateTimeImmutable { return $this->paidAt; }
    public function setPaidAt(?DateTimeImmutable $d): static { $this->paidAt = $d; return $this; }

    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
}
