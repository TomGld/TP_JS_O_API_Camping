<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /**
     * @var Collection<int, EquipmentRental>
     */
    #[ORM\OneToMany(targetEntity: EquipmentRental::class, mappedBy: 'equipment')]
    private Collection $equipmentRentals;

    public function __construct()
    {
        $this->equipmentRentals = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, EquipmentRental>
     */
    public function getEquipmentRentals(): Collection
    {
        return $this->equipmentRentals;
    }

    public function addEquipmentRental(EquipmentRental $equipmentRental): static
    {
        if (!$this->equipmentRentals->contains($equipmentRental)) {
            $this->equipmentRentals->add($equipmentRental);
            $equipmentRental->setEquipment($this);
        }

        return $this;
    }

    public function removeEquipmentRental(EquipmentRental $equipmentRental): static
    {
        if ($this->equipmentRentals->removeElement($equipmentRental)) {
            // set the owning side to null (unless already changed)
            if ($equipmentRental->getEquipment() === $this) {
                $equipmentRental->setEquipment(null);
            }
        }

        return $this;
    }
}
