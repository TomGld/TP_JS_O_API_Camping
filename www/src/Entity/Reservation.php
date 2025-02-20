<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;        
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;



#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    //autorisation des route que l'on veut acceder
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['reservation:read']],
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?int $id = null;

    #[Groups(['reservation:read'])]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rental $rental = null;

    #[Groups(['reservation:read'])]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $renter = null;

    #[Groups(['reservation:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[Groups(['reservation:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    #[Groups(['reservation:read'])]
    #[ORM\Column]
    private ?int $nbrAdult = null;

    #[Groups(['reservation:read'])]
    #[ORM\Column]
    private ?int $nbrMinor = null;

    #[Groups(['reservation:read'])]
    #[ORM\Column]
    private ?string $status = null;

    #[Groups(['reservation:read'])]
    #[ORM\Column]
    private ?int $checked = null;

    #[Groups(['reservation:read'])]
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
