<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $season_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $season_end = null;

    #[ORM\OneToOne(mappedBy: 'season', cascade: ['persist', 'remove'])]
    private ?Price $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getSeasonStart(): ?\DateTimeInterface
    {
        return $this->season_start;
    }

    public function setSeasonStart(\DateTimeInterface $season_start): static
    {
        $this->season_start = $season_start;

        return $this;
    }

    public function getSeasonEnd(): ?\DateTimeInterface
    {
        return $this->season_end;
    }

    public function setSeasonEnd(\DateTimeInterface $season_end): static
    {
        $this->season_end = $season_end;

        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): static
    {
        // set the owning side of the relation if necessary
        if ($price->getSeason() !== $this) {
            $price->setSeason($this);
        }

        $this->price = $price;

        return $this;
    }
}
