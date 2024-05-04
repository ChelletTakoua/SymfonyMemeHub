<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[Assert\Expression(
    "this.getURL() !== null or this.getImg() !== null",
    message: "Either URL or img must be provided."
)]
class Template implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $URL = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $img = null;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Meme::class)]
    private Collection $memes;

    public function __construct()
    {
        $this->memes = new ArrayCollection();
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

    public function getURL(): ?string
    {
        return $this->URL;
    }

    public function setURL(?string $URL): static
    {
        $this->URL = $URL;

        return $this;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function setImg($img): static
    {
        $this->img = $img;

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
            $meme->setTemplate($this);
        }

        return $this;
    }

    public function removeMeme(Meme $meme): static
    {
        if ($this->memes->removeElement($meme)) {
            // set the owning side to null (unless already changed)
            if ($meme->getTemplate() === $this) {
                $meme->setTemplate(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->URL,
            //'img' => $this->img,
        ];
    }
}
