<?php

namespace App\Entity;

use App\Repository\BlockedMemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: BlockedMemeRepository::class),
    ORM\HasLifecycleCallbacks()
]
class BlockedMeme implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blockedMemes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $admin = null;

    #[ORM\OneToMany(mappedBy: 'blockedMeme', targetEntity: report::class)]
    private Collection $report;

    #[ORM\OneToOne(targetEntity: Meme::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meme $meme = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $blockDate = null;

    public function __construct()
    {
        $this->report = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, report>
     */
    public function getReport(): Collection
    {
        return $this->report;
    }

    public function addReportId(Report $reportId): static
    {
        if (!$this->report->contains($reportId)) {
            $this->report->add($reportId);
            $reportId->setBlockedMeme($this);
        }

        return $this;
    }

    public function removeReportId(Report $reportId): static
    {
        if ($this->report->removeElement($reportId)) {
            // set the owning side to null (unless already changed)
            if ($reportId->getBlockedMeme() === $this) {
                $reportId->setBlockedMeme(null);
            }
        }

        return $this;
    }

    public function getMeme(): ?Meme
    {
        return $this->meme;
    }

    public function setMeme(Meme $meme): static
    {
        $this->meme = $meme;

        return $this;
    }

    public function getBlockDate(): ?\DateTimeInterface
    {
        return $this->blockDate;
    }

    public function setBlockDate(\DateTimeInterface $blockDate): static
    {
        $this->blockDate = $blockDate;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPersist(): void
    {
        $this->blockDate = new \DateTime();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'meme' => $this->meme,
            'admin' => $this->admin,
            'report' => $this->report,
        ];
    }
}
