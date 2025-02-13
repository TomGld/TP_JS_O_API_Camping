<?php

namespace App\Controller;

use App\Entity\Rental;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/reservation')]
final class AdminReservationController extends AbstractController
{
    #[Route(name: 'app_admin_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_admin_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        //Récupérer le rental par l'id dans l'url
        $rentalId = $reservationRepository->find($request->get('id'));
        //Récupérer les données du rental par l'id
        $rental = $entityManager->getRepository(Rental::class)->findRentalInfoById($rentalId);


        //Extraire les seasons et les equipment
        $seasons = [];
        $equipments = [];
        foreach ($rental as $rental) {
            $seasons[] = [
            'label' => $rental['season_label'],
            'date_start' => $rental['season_start'],
            'date_end' => $rental['season_end']
            ];
            $equipments[] = $rental['rental_equipment_label'];
        }

        //Supprimer season_label, season_start, season_end de $rental
        unset($rental['season_label']);
        unset($rental['season_start']);
        unset($rental['season_end']);
        unset($rental['rental_equipment_label']);

        //Supprimer les doublons dans les équipments et les seasons
        $equipments = array_values(array_unique($equipments));
        $seasons = array_map("unserialize", array_unique(array_map("serialize", $seasons)));

        //Ajouter les seasons et les equipment dans le $rental
        $rental['seasons'] = $seasons;
        $rental['equipments'] = $equipments;


        //AddFlash(nom du flach, message)
        // rediret to route

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/reservation/new.html.twig', [
            'reservation' => $reservation,
            'rental' => $rental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('admin/reservation/show.html.twig', [
            'reservation' => $reservation,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }
        
        

        return $this->redirectToRoute('app_admin_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
