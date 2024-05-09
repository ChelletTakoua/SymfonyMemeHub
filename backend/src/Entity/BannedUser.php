<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\BannedUserRepository;

#[
    ORM\Entity(repositoryClass: BannedUserRepository::class),
    ORM\HasLifecycleCallbacks()
]
class BannedUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'bannedUser', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $banStartDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $banDuration = null;

    #[ORM\Column(length: 255)]
    private ?string $reason = null;

    #[ORM\ManyToOne(inversedBy: 'bannedUsers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $admin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $banEndDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getbanStartDate(): ?\DateTimeInterface
    {
        return $this->banStartDate;
    }

    public function setbanStartDate(\DateTimeInterface $banStartDate): static
    {
        $this->banStartDate = $banStartDate;

        return $this;
    }

    public function getBanDuration(): ?int
    {
        return $this->banDuration;
    }

    public function setBanDuration(int $banDuration): static
    {
        $this->banDuration = $banDuration;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPersist(): void
    {
        $this->banStartDate = new \DateTime();
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): static
    {
        $this->admin = $admin;

        return $this;
    }

    public function getBanEndDate(): ?\DateTimeInterface
    {
        return $this->banEndDate;
    }

    public function setBanEndDate(?\DateTimeInterface $banEndDate): static
    {
        $this->banEndDate = $banEndDate;

        return $this;
    }
}
