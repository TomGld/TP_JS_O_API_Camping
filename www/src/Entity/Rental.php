<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?int $nbrLocalization = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeRental $typeRental = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Price>
     */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'rental')]
    private Collection $prices;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'rental')]
    private Collection $reservations;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'rentals')]
    private Collection $equipments;


    public function __construct()
    {

        $this->prices = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->equipments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getNbrLocalization(): ?int
    {
        return $this->nbrLocalization;
    }

    public function setNbrLocalization(int $nbrLocalization): static
    {
        $this->nbrLocalization = $nbrLocalization;

        return $this;
    }

    public function getTypeRental(): ?TypeRental
    {
        return $this->typeRental;
    }

    public function setTypeRental(?TypeRental $typeRental): static
    {
        $this->typeRental = $typeRental;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }


    /**
     * @return Collection<int, Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setRental($this);
        }

        return $this;
    }

    public function removePrice(Price $price): static
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getRental() === $this) {
                $price->setRental(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setRental($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getRental() === $this) {
                $reservation->setRental(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        $this->equipments->removeElement($equipment);

        return $this;
    }
}
