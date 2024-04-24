<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $report_date = null;

    #[ORM\Column(length: 15)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    private ?meme $meme_id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    private ?user $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'report_id')]
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
        return $this->report_date;
    }

    public function setReportDate(\DateTimeInterface $report_date): static
    {
        $this->report_date = $report_date;

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

    public function getMemeId(): ?meme
    {
        return $this->meme_id;
    }

    public function setMemeId(?meme $meme_id): static
    {
        $this->meme_id = $meme_id;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): static
    {
        $this->user_id = $user_id;

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
}
