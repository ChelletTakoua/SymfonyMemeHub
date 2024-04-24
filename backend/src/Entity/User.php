<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reg_date = null;

    #[ORM\Column(length: 20)]
    private ?string $role = null;

    #[ORM\Column]
    private ?bool $is_verified = null;

    #[ORM\Column(type: Types::BLOB)]
    private $profile_pic = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Meme::class)]
    private Collection $memes;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Report::class)]
    private Collection $reports;

    #[ORM\OneToMany(mappedBy: 'admin_id', targetEntity: BlockedMeme::class)]
    private Collection $blockedMemes;

    #[ORM\OneToOne(mappedBy: 'user_id', cascade: ['persist', 'remove'])]
    private ?BannedUser $bannedUser = null;

    public function __construct()
    {
        $this->memes = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->blockedMemes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRegDate(): ?\DateTimeInterface
    {
        return $this->reg_date;
    }

    public function setRegDate(\DateTimeInterface $reg_date): static
    {
        $this->reg_date = $reg_date;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): static
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function getProfilePic()
    {
        return $this->profile_pic;
    }

    public function setProfilePic($profile_pic): static
    {
        $this->profile_pic = $profile_pic;

        return $this;
    }

    /**
     * @return Collection<int, Meme>
     */
    public function getMemes(): Collection
    {
        return $this->memes;
    }

    public function addMeme(Meme $meme): static
    {
        if (!$this->memes->contains($meme)) {
            $this->memes->add($meme);
            $meme->setUserId($this);
        }

        return $this;
    }

    public function removeMeme(Meme $meme): static
    {
        if ($this->memes->removeElement($meme)) {
            // set the owning side to null (unless already changed)
            if ($meme->getUserId() === $this) {
                $meme->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUserId($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUserId() === $this) {
                $like->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setUserId($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getUserId() === $this) {
                $report->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlockedMeme>
     */
    public function getBlockedMemes(): Collection
    {
        return $this->blockedMemes;
    }

    public function addBlockedMeme(BlockedMeme $blockedMeme): static
    {
        if (!$this->blockedMemes->contains($blockedMeme)) {
            $this->blockedMemes->add($blockedMeme);
            $blockedMeme->setAdminId($this);
        }

        return $this;
    }

    public function removeBlockedMeme(BlockedMeme $blockedMeme): static
    {
        if ($this->blockedMemes->removeElement($blockedMeme)) {
            // set the owning side to null (unless already changed)
            if ($blockedMeme->getAdminId() === $this) {
                $blockedMeme->setAdminId(null);
            }
        }

        return $this;
    }

    public function getBannedUser(): ?BannedUser
    {
        return $this->bannedUser;
    }

    public function setBannedUser(BannedUser $bannedUser): static
    {
        // set the owning side of the relation if necessary
        if ($bannedUser->getUserId() !== $this) {
            $bannedUser->setUserId($this);
        }

        $this->bannedUser = $bannedUser;

        return $this;
    }
}
