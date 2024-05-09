<?php

namespace App\Entity;

use App\Repository\MemeRepository;
use App\Traits\SoftDeleteTrait;
use App\Annotation\PreSoftDelete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[
    ORM\Entity(repositoryClass: MemeRepository::class),
    ORM\HasLifecycleCallbacks()
]
//TODO : Increment the number of likes when a like is added here is the code to do it:

//        $meme = $like->getMeme();
//        $meme->setNumLikes($meme->getNumLikes() + 1);
//        $this->entityManager->persist($meme);
//        $this->entityManager->flush();
class Meme implements JsonSerializable
{

    use SoftDeleteTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private ?int $numLikes = null;

    #[ORM\Column(type: Types::BLOB)]
    private $resultImg = null;

    #[ORM\ManyToOne(inversedBy: 'memes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'memes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Template $template = null;

    #[ORM\OneToMany(mappedBy: 'meme', targetEntity: TextBlock::class)]
    private Collection $textBlocks;

    #[ORM\OneToMany(mappedBy: 'meme', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'meme', targetEntity: Report::class)]
    private Collection $reports;
    private ?User $currentUser=null;

    public function setCurrentUser(?User $user): void
    {
        $this->currentUser = $user;
    }

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
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getNumLikes(): ?int
    {
        return $this->numLikes;
    }

    public function setNumLikes(int $numLikes): static
    {
        $this->numLikes = $numLikes;

        return $this;
    }

    public function getResultImg(): mixed
    {
        return $this->resultImg;
    }

    public function setResultImg($resultImg): static
    {
        $this->resultImg = $resultImg;

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

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): static
    {
        $this->template = $template;

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
            $textBlock->setMeme($this);
        }

        return $this;
    }

    public function removeTextBlock(TextBlock $textBlock): static
    {
        if ($this->textBlocks->removeElement($textBlock)) {
            // set the owning side to null (unless already changed)
            if ($textBlock->getMeme() === $this) {
                $textBlock->setMeme(null);
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
            $like->setMeme($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getMeme() === $this) {
                $like->setMeme(null);
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
            $report->setMeme($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getMeme() === $this) {
                $report->setMeme(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function onPersist():void
    {
        $this->creationDate = new \DateTime();
        $this->numLikes = 0;
    }

    #[PreSoftDelete]
    public function preSoftDelete(): void
    {

    }

    public function jsonSerialize(): mixed
    {
        $isLikedByUser = false;
        if ($this->currentUser) {
            foreach ($this->currentUser->getLikes() as $like) {
                if ($like->getMeme() === $this) {
                    $isLikedByUser = true;
                    break;
                }
            }
        }
        
        return [
            "id" => $this->getId(),
            "template" => $this->getTemplate(),
            "user_id" =>  $this->getUser()->getId(),
            "nb_likes" => $this->getNbLikes(),
            "creation_date" => $this->getCreationDate(),
            "text_blocks" => $this->getTextBlocks(),
            "deletedAt" => $this->getDeletedAt(),//TODO: to remove
            "result_img" => stream_get_contents($this->getResultImg()),
            'liked' => $isLikedByUser,
        ];
    }


    private function getNbLikes()
    {
        return count($this->getLikes());

    }

    public function isDeleted()
    {
        return $this->deletedAt!=null;
    }
}
