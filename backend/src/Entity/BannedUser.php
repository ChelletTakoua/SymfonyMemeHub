<?php

namespace App\Entity;

use App\Repository\BannedUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BannedUserRepository::class)]
class BannedUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'bannedUser', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ban_date = null;

    #[ORM\Column]
    private ?int $ban_duration = null;

    #[ORM\Column(length: 255)]
    private ?string $reason = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(user $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getBanDate(): ?\DateTimeInterface
    {
        return $this->ban_date;
    }

    public function setBanDate(\DateTimeInterface $ban_date): static
    {
        $this->ban_date = $ban_date;

        return $this;
    }

    public function getBanDuration(): ?int
    {
        return $this->ban_duration;
    }

    public function setBanDuration(int $ban_duration): static
    {
        $this->ban_duration = $ban_duration;

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
}
