<?php
// src/Entity/Auction.php

namespace App\Entity;

use App\Repository\AuctionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuctionRepository::class)]
class Auction
{
    public const STATUS_OPEN   = 'OPEN';
    public const STATUS_CLOSED = 'CLOSED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column]
    private ?int $bidCost = null;

    #[ORM\Column(length: 10)]
    private ?string $status = null;

    #[ORM\OneToOne(targetEntity: Bid::class)]
    private ?Bid $winnerBid = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $paymentReceived = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $productDelivered = false;

    // … getters & setters …

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getBidCost(): ?int
    {
        return $this->bidCost;
    }

    public function setBidCost(int $bidCost): static
    {
        $this->bidCost = $bidCost;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getWinnerBid(): ?Bid
    {
        return $this->winnerBid;
    }

    public function setWinnerBid(?Bid $winnerBid): static
    {
        $this->winnerBid = $winnerBid;
        return $this;
    }

    public function isPaymentReceived(): bool
    {
        return $this->paymentReceived;
    }

    public function setPaymentReceived(bool $received): static
    {
        $this->paymentReceived = $received;
        return $this;
    }

    public function isProductDelivered(): bool
    {
        return $this->productDelivered;
    }

    public function setProductDelivered(bool $delivered): static
    {
        $this->productDelivered = $delivered;
        return $this;
    }
}
