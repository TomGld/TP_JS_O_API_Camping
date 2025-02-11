<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Pricing>
     */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'season_id')]
    private Collection $prices;

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

    /**
     * @return Collection<int, Pricing>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setSeason($this);
        }

        return $this;
    }

    public function removePrice(Price $price): static
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getSeason() === $this) {
                $price->setSeason(null);
            }
        }

        return $this;
    }

}
