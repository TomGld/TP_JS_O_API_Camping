<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


final class ApiReservationController extends AbstractController
{


    //API

    #[Route('/reservation/api/js', name: 'app_reservation_json', methods: ['GET'])]
    public function getReservationDayNow(ReservationRepository $reservationRepository): JsonResponse
    {
        // On crée un datetime pour récupérer la date du jour
        $dateNow = new \DateTime('today');
        // On récupère les réservations qui commencent aujourd'hui
        $reservationStart = $reservationRepository->findReservationStartNow($dateNow);
        // On récupère les réservations qui se terminent aujourd'hui
        $reservationEnd = $reservationRepository->findReservationEndNow($dateNow);


        // On retourne les réservations qui commencent et se terminent aujourd'hui en JSON
        return new JsonResponse([
            'reservationsStart' => $reservationStart,
            'reservationsEnd' => $reservationEnd
        ]);
    }






}
