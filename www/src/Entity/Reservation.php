<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rental $rental = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $renter = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column]
    private ?int $nbrAdult = null;

    #[ORM\Column]
    private ?int $nbrMinor = null;

    #[ORM\Column]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $checked = null;

    #[ORM\Column]
    private ?float $appliedPriceTotal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRental(): ?Rental
    {
        return $this->rental;
    }

    public function setRental(?Rental $rental): static
    {
        $this->rental = $rental;

        return $this;
    }

    public function getRenter(): ?User
    {
        return $this->renter;
    }

    public function setRenter(?User $renter): static
    {
        $this->renter = $renter;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getNbrAdult(): ?int
    {
        return $this->nbrAdult;
    }

    public function setNbrAdult(int $nbrAdult): static
    {
        $this->nbrAdult = $nbrAdult;

        return $this;
    }

    public function getNbrMinor(): ?int
    {
        return $this->nbrMinor;
    }

    public function setNbrMinor(int $nbrMinor): static
    {
        $this->nbrMinor = $nbrMinor;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getChecked(): ?int
    {
        return $this->checked;
    }

    public function setChecked(int $checked): static
    {
        $this->checked = $checked;

        return $this;
    }

    public function getAppliedPriceTotal(): ?float
    {
        return $this->appliedPriceTotal;
    }

    public function setAppliedPriceTotal(float $appliedPriceTotal): static
    {
        $this->appliedPriceTotal = $appliedPriceTotal;

        return $this;
    }
}
