<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function findReservationByRentalId($rentalId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.rental = :rentalId')
            ->setParameter('rentalId', $rentalId)
            ->getQuery()
            ->getResult();
        
    }
    /**
     * @param $renterId
     * @return Reservation[]
     */
    public function findReservationByRenterId($renterId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.renter = :renterId')
            ->setParameter('renterId', $renterId)
            ->getQuery()
            ->getResult();
        
    }

    //Méthode pour récupérer les réservations avec date_start 
    public function findReservationStartNow($dateNow): array
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select([
            'r.id',
            'rt.label as rentalType',
            'ru.firstname as renterFirstname',
            'ru.lastname as renterLastname',
            'r.dateStart',
            'r.dateEnd',
            'r.nbrAdult',
            'r.nbrMinor',
            'r.checked',
            'ra.nbrLocalization'
        ])
            ->from(Reservation::class, 'r')
            ->leftJoin('r.rental', 'ra')
            ->leftJoin('r.renter', 'ru')
            ->leftJoin('ra.typeRental', 'rt')
            ->where('r.dateStart = :dateNow')
            ->setParameter('dateNow', $dateNow)
            ->getQuery();

            $result = $query->getResult();

        return $result;
    }


    //Méthode pour récupérer les réservations avec date_start 
    public function findReservationEndNow($dateNow): array
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        $query = $qb->select([
            'r.id',
            'ra.id as rentalId',
            'ru.id as renterId',
            'ru.firstname as renterFirstname',
            'ru.lastname as renterLastname',
            'r.dateStart',
            'r.dateEnd',
            'r.nbrAdult',
            'r.nbrMinor',
            'r.status',
            'r.checked',
            'r.appliedPriceTotal'
        ])
            ->from(Reservation::class, 'r')
            ->leftJoin('r.rental', 'ra')
            ->leftJoin('r.renter', 'ru')
            ->where('r.dateEnd = :dateNow')
            ->setParameter('dateNow', $dateNow)
            ->getQuery();

        $result = $query->getResult();

        return $result;
    }

}
