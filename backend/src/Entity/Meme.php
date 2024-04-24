<?php

namespace App\Entity;

use App\Repository\MemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemeRepository::class)]
class Meme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\Column]
    private ?int $num_likes = null;

    #[ORM\Column(type: Types::BLOB)]
    private $result_img = null;

    #[ORM\ManyToOne(inversedBy: 'memes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'memes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?template $template_id = null;

    #[ORM\OneToMany(mappedBy: 'meme_id', targetEntity: TextBlock::class)]
    private Collection $textBlocks;

    #[ORM\OneToMany(mappedBy: 'meme_id', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'meme_id', targetEntity: Report::class)]
    private Collection $reports;

    public function __construct()
    {
        $this->textBlocks = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getNumLikes(): ?int
    {
        return $this->num_likes;
    }

    public function setNumLikes(int $num_likes): static
    {
        $this->num_likes = $num_likes;

        return $this;
    }

    public function getResultImg()
    {
        return $this->result_img;
    }

    public function setResultImg($result_img): static
    {
        $this->result_img = $result_img;

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

    public function getTemplateId(): ?template
    {
        return $this->template_id;
    }

    public function setTemplateId(?template $template_id): static
    {
        $this->template_id = $template_id;

        return $this;
    }

    /**
     * @return Collection<int, TextBlock>
     */
    public function getTextBlocks(): Collection
    {
        return $this->textBlocks;
    }

    public function addTextBlock(TextBlock $textBlock): static
    {
        if (!$this->textBlocks->contains($textBlock)) {
            $this->textBlocks->add($textBlock);
            $textBlock->setMemeId($this);
        }

        return $this;
    }

    public function removeTextBlock(TextBlock $textBlock): static
    {
        if ($this->textBlocks->removeElement($textBlock)) {
            // set the owning side to null (unless already changed)
            if ($textBlock->getMemeId() === $this) {
                $textBlock->setMemeId(null);
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
            $like->setMemeId($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getMemeId() === $this) {
                $like->setMemeId(null);
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
            $report->setMemeId($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getMemeId() === $this) {
                $report->setMemeId(null);
            }
        }

        return $this;
    }
}
