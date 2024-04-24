<?php

namespace App\Entity;

use App\Repository\TextBlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TextBlockRepository::class)]
class TextBlock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $text = null;

    #[ORM\Column]
    private ?int $x = null;

    #[ORM\Column]
    private ?int $y = null;

    #[ORM\Column(length: 10)]
    private ?string $font_size = null;

    #[ORM\ManyToOne(inversedBy: 'textBlocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?meme $meme_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(int $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(int $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function getFontSize(): ?string
    {
        return $this->font_size;
    }

    public function setFontSize(string $font_size): static
    {
        $this->font_size = $font_size;

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
}
