<?php

namespace App\Entity;

use App\Repository\BlockedMemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlockedMemeRepository::class)]
class BlockedMeme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blockedMemes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $admin_id = null;

    #[ORM\OneToMany(mappedBy: 'blockedMeme', targetEntity: report::class)]
    private Collection $report_id;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?meme $meme_id = null;

    public function __construct()
    {
        $this->report_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdminId(): ?user
    {
        return $this->admin_id;
    }

    public function setAdminId(?user $admin_id): static
    {
        $this->admin_id = $admin_id;

        return $this;
    }

    /**
     * @return Collection<int, report>
     */
    public function getReportId(): Collection
    {
        return $this->report_id;
    }

    public function addReportId(report $reportId): static
    {
        if (!$this->report_id->contains($reportId)) {
            $this->report_id->add($reportId);
            $reportId->setBlockedMeme($this);
        }

        return $this;
    }

    public function removeReportId(report $reportId): static
    {
        if ($this->report_id->removeElement($reportId)) {
            // set the owning side to null (unless already changed)
            if ($reportId->getBlockedMeme() === $this) {
                $reportId->setBlockedMeme(null);
            }
        }

        return $this;
    }

    public function getMemeId(): ?meme
    {
        return $this->meme_id;
    }

    public function setMemeId(meme $meme_id): static
    {
        $this->meme_id = $meme_id;

        return $this;
    }
}
