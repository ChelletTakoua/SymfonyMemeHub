<?php

namespace App\Entity;

use App\Repository\TextBlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TextBlockRepository::class)]
class TextBlock implements \JsonSerializable
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
    private ?string $fontSize = null;

    #[ORM\ManyToOne(inversedBy: 'textBlocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meme $meme = null;

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
        return $this->fontSize;
    }

    public function setFontSize(string $fontSize): static
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    public function getMeme(): ?Meme
    {
        return $this->meme;
    }

    public function setMeme(?Meme $meme): static
    {
        $this->meme = $meme;

        return $this;
    }


    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'x' => $this->x,
            'y' => $this->y,
            'font_size' => $this->fontSize,
        ];
    }
}
