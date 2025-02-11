<?php

namespace App\Repository;

use App\Entity\Equipment;
use App\Entity\Price;
use App\Entity\Rental;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }


     /**
      * Function pour retourner tous des rentals
        * @return Rental[]
      */
    public function findAllRentals(): array
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        //Sélectionner les champs nécessaires
        $query = $qb->select([
            'r.id',
            'r.title',
            'r.description',
            'r.capacity',
            'r.nbrLocalization', //Par la propriété dans l'entity
            'r.isActive', //Par la propriété dans l'entity
            'r.image',
        ])
        ->from(Rental::class, 'r')
        ->join('r.typeRental', 't')
        ->getQuery();

        $results = $query->getResult();

        return $results;
    }

    // /**
    //  * Function pour retourner un rental, son type_rental_id, ses equipment, ses seasons et ses price
    //  */
    // public function findAllInfoRental()
    // {
    //     $entityManager = $this->getEntityManager();
    //     $qb = $entityManager->createQueryBuilder();

    //     //Sélectionner les champs nécessaires
    //     $query = $qb->select([
    //         'r.id',
    //         'r.title',
    //         'r.description',
    //         'r.capacity',
    //         'r.nbrLocalization', //Par la propriété dans l'entity
    //         'r.isActive', //Par la propriété dans l'entity
    //         'r.image',
    //         't.id as type_rental_id',
    //         't.label as type_rental_label',
    //         're.id as rental_equipment_id',
    //         're.label as rental_equipment_label',
    //         's.id as season_id',
    //         's.label as season_label',
    //         's.season_start',
    //         's.season_end',
    //     ])
    //     ->from(Rental::class, 'r')
    //     ->join('r.typeRental', 't')
    //     ->join(Equipment::class, 're')
    //     ->join(Season::class, 's')
    //     ->join(Price::class, 'p')
    //     ->getQuery();

    //     $results = $query->getResult();

    //     return $results;
    // }

    /**
     * Méthode pour retourner un rental (title, capacity, nbrLocalization, image, typeRental)
     */
    public function findAllRentalCard()
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        //Sélectionner les champs nécessaires
        $query = $qb->select([
            'r.id',
            'r.title',
            'r.capacity',
            'r.nbrLocalization',
            'r.isActive',
            'r.image',
            't.label as type_rental_label',
        ])
        ->from(Rental::class, 'r')
        ->join('r.typeRental', 't')
        ->getQuery();

        $results = $query->getResult();

        return $results;
    }

    /**
     * Méthode pour retourner un rental, son image, ses prices et ses seasons, son typeRental, ses equipments

     */
    public function findRentalInfoById($id)
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        //Sélectionner les champs nécessaires
        $query = $qb->select([
            'r.id',
            'r.title',
            'r.description',
            'r.capacity',
            'r.nbrLocalization',
            'r.isActive',
            'r.image',
            't.label as type_rental_label',
            're.label as rental_equipment_label',
            'p.pricePerNight',
            's.label as season_label',
            's.season_start',
            's.season_end',
        ])
        ->from(Rental::class, 'r')
        ->join('r.typeRental', 't')
        ->leftJoin('r.equipments', 're')
        ->leftJoin('r.prices', 'p')
        ->leftJoin('p.season', 's')
        ->where('r.id = :id')
        ->setParameter('id', $id)
        ->getQuery();

        $results = $query->getResult();



        return $results;
    }

}
