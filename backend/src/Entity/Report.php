<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ReportRepository::class),
    ORM\HasLifecycleCallbacks()
]

class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reportDate = null;

    #[ORM\Column(length: 15)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meme $meme = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'report')]
    private ?BlockedMeme $blockedMeme = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getReportDate(): ?\DateTimeInterface
    {
        return $this->reportDate;
    }

    public function setReportDate(\DateTimeInterface $reportDate): static
    {
        $this->reportDate = $reportDate;

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

    public function getMeme(): ?meme
    {
        return $this->meme;
    }

    public function setMeme(?meme $meme): static
    {
        $this->meme = $meme;

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

    public function getBlockedMeme(): ?BlockedMeme
    {
        return $this->blockedMeme;
    }

    public function setBlockedMeme(?BlockedMeme $blockedMeme): static
    {
        $this->blockedMeme = $blockedMeme;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPersist(): void
    {
        $this->reportDate = new \DateTime();
        $this->status = 'pending';
    }

}
