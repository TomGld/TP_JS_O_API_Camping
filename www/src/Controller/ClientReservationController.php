<?php

namespace App\Controller;

use App\Entity\Rental;
use App\Entity\Reservation;
use App\Form\ClientReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/myreservations')]
final class ClientReservationController extends AbstractController
{

    //Créer la méthode pour calculer le prix (en fonction des dates dans seasons et price_per_night dans price) entrées par l'utilisateur dans le formulaire.
    public function calculatePrice(\DateTime $dateStart, \DateTime $dateEnd, $seasons)
    {
        $price = 0;
        $dateStart = $dateStart->setTime(0, 0, 0);
        $dateEnd = $dateEnd->setTime(0, 0, 0);

        // Exclure le dernier jour pour le calcul du prix
        $dateEndForPrice = (clone $dateEnd)->modify('-1 day');

        foreach ($seasons as $season) {
            $seasonStart = $season['date_start'];
            $seasonEnd = $season['date_end'];

            // Si la période de réservation est incluse dans une saison
            if ($dateStart >= $seasonStart && $dateEndForPrice <= $seasonEnd) {
                $price += $season['pricePerNight'] * ($dateStart->diff($dateEndForPrice)->days + 1);
                // Si la période de réservation commence et se termine en dehors d'une saison
            } elseif ($dateStart >= $seasonStart && $dateStart <= $seasonEnd && $dateEndForPrice >= $seasonEnd) {
                $price += $season['pricePerNight'] * ($dateStart->diff($seasonEnd)->days + 1);
                // Si la période de réservation commence et se termine en dehors d'une saison
            } elseif ($dateStart <= $seasonStart && $dateEndForPrice >= $seasonStart && $dateEndForPrice <= $seasonEnd) {
                $price += $season['pricePerNight'] * ($seasonStart->diff($dateEndForPrice)->days + 1);
                // Si la période de réservation commence et se termine en dehors d'une saison
            } elseif ($dateStart <= $seasonStart && $dateEndForPrice >= $seasonEnd) {
                $price += $season['pricePerNight'] * ($seasonStart->diff($seasonEnd)->days + 1);
            }
        }

        return $price;
    }


    #[Route(name: 'app_client_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        //Définition pour ROLE_USER même si ROLE_ADMIN a accès
        $this->denyAccessUnlessGranted('ROLE_USER');

        //Récupération de l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('client/reservation/index.html.twig', [
            'reservations' => $reservationRepository->findReservationByRenterId($user),
        ]);
    }

    #[Route('/new/{id}', name: 'app_client_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ClientReservationType::class, $reservation, ['is_edit' => false]);
        $form->handleRequest($request);

        // Récupération du rental
        $rentalId = $request->get('id');
        $rental = $entityManager->getRepository(Rental::class)->findRentalInfoById($rentalId);
        if (!$rental) {
            throw $this->createNotFoundException('Location non trouvée.');
        }


        // Traitement du rental
        $seasons = [];
        $equipments = [];
        foreach ($rental as $rental) {
            $seasons[] = [
                'label' => $rental['season_label'],
                'date_start' => $rental['season_start'],
                'date_end' => $rental['season_end'],
                'pricePerNight' => $rental['pricePerNight']
            ];
            $equipments[] = $rental['rental_equipment_label'];
        }

        unset($rental['season_label']);
        unset($rental['season_start']);
        unset($rental['season_end']);
        unset($rental['rental_equipment_label']);


        $equipments = array_values(array_unique($equipments));
        $seasons = array_map("unserialize", array_unique(array_map("serialize", $seasons)));

        $rental['seasons'] = $seasons;
        $rental['equipments'] = $equipments;


        //Initialiser la variable $calculatedPrice
        $calculatedPrice = null;


        //Récupération de l'user connecté en session
        $user = $this->getUser();


        if ($form->isSubmitted()) {
            if ($request->request->get('action') === 'calculate') {
                if (!$rental['isActive']) {
                    $this->addFlash('danger', 'Annonce indisponible à la location.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                $dateStart = $reservation->getDateStart();
                $dateEnd = $reservation->getDateEnd();

                //vérification des dates de début et de fin
                if ($dateStart >= $dateEnd) {
                    $this->addFlash('danger', 'La date de début doit être inférieure à la date de fin.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                if ($dateStart < new \DateTime()) {
                    $this->addFlash('danger', 'La date de début doit être supérieure à la date actuelle.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                // Vérifiez que les dates ne sont pas nulles
                if ($dateStart && $dateEnd) {
                    $calculatedPrice = $this->calculatePrice($dateStart, $dateEnd, $rental['seasons']);
                    if ($calculatedPrice === 0) {
                        $this->addFlash('danger', 'Les prix ne sont pas déclarés pour cette période, il est donc impossible d\'effectuer une réservation pour ces dates.');
                        return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                    }
                }
            } elseif ($form->isValid()) {
                if (!$rental['isActive']) {
                    $this->addFlash('danger', 'Annonce indisponible à la location.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }
                //Prendre les dates soumises par l'utilisateur et appeler la methode calculatePrice pour obtenir le prix total et le stocker dans la bdd sous applied_price_total
                $calculatedPrice = $this->calculatePrice($reservation->getDateStart(), $reservation->getDateEnd(), $rental['seasons']);
                $reservation->setAppliedPriceTotal($calculatedPrice);

                $nbrAdults = $reservation->getNbrAdult();
                $nbrMinors = $reservation->getNbrMinor();
                $totalGuests = $nbrAdults + $nbrMinors;

                if ($nbrAdults < 1) {
                    $this->addFlash('danger', 'Il doit y avoir au moins un adulte.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                if ($nbrMinors < 0) {
                    $this->addFlash('danger', 'Le nombre de mineurs doit être positif ou nul.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                if ($totalGuests > $rental['capacity']) {
                    $this->addFlash('danger', 'Le nombre total de personnes ne doit pas dépasser la capacité de la location.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                if ($reservation->getDateStart() >= $reservation->getDateEnd()) {
                    $this->addFlash('danger', 'La date de début doit être inférieure à la date de fin.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                if ($reservation->getDateStart() < new \DateTime()) {
                    $this->addFlash('danger', 'La date de début doit être supérieure à la date actuelle.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                // Vérifier si les prix sont déclarés pour les dates de réservation
                $priceDeclared = false;
                foreach ($rental['seasons'] as $season) {
                    if (($reservation->getDateStart() >= $season['date_start'] && $reservation->getDateStart() <= $season['date_end']) ||
                        ($reservation->getDateEnd() >= $season['date_start'] && $reservation->getDateEnd() <= $season['date_end'])) {
                        $priceDeclared = true;
                        break;
                    }
                }

                if (!$priceDeclared) {
                    $this->addFlash('danger', 'Les prix ne sont pas encore déclarés pour cette période, il est donc impossible d\'effectuer une réservation pour ces dates.');
                    return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                }

                $reservations = $reservationRepository->findReservationByRentalId($rentalId);
                foreach ($reservations as $res) {
                    if ($reservation->getDateStart() >= $res->getDateStart() && $reservation->getDateStart() <= $res->getDateEnd()) {
                        $this->addFlash('danger', 'La date de début n\'est pas disponible.');
                        return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                    }
                    if ($reservation->getDateEnd() >= $res->getDateStart() && $reservation->getDateEnd() <= $res->getDateEnd()) {
                        $this->addFlash('danger', 'La date de fin n\'est pas disponible.');
                        return $this->redirectToRoute('app_client_reservation_new', ['id' => $rentalId]);
                    }
                }
                
                //Set quand même le renter pour éviter les injections
                $reservation->setRenter($user);
                //Set le status à 1 par défaut (validée)
                $reservation->setStatus(1);
                //set checked à 2
                $reservation->setChecked(2);
                $reservation->setRental($entityManager->getRepository(Rental::class)->find($rentalId));
                $entityManager->persist($reservation);
                $entityManager->flush();

                return $this->redirectToRoute('app_client_reservation_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('client/reservation/new.html.twig', [
            'reservation' => $reservation,
            'rental' => $rental,
            'form' => $form,
            'calculatedPrice' => $calculatedPrice,
        ]);
    }

    #[Route('/{id}', name: 'app_client_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {

        //Reconstruire un tableau associatif pour le component _detail_card.html.twig
        $rental = [
            'id' => $reservation->getRental()->getId(),
            'type_rental_label' => $reservation->getRental()->getTypeRental()->getLabel(),
            'title' => $reservation->getRental()->getTitle(),
            'description' => $reservation->getRental()->getDescription(),
            'capacity' => $reservation->getRental()->getCapacity(),
            'nbrLocalization' => $reservation->getRental()->getNbrLocalization(),
            'isActive' => $reservation->getRental()->IsActive(),
            'image' => $reservation->getRental()->getImage(),
            'equipments' => $reservation->getRental()->getEquipments()->toArray(),
            'price' => $reservation->getAppliedPriceTotal(),
            'seasons' => [],
        ];

        //Traitements

        //Traitement de sécurisation pour les équipements
        //Extraire les equipments
        $equipments = [];
        foreach ($rental['equipments'] as $equipment) {
            $equipments[] = $equipment->getLabel();
        }
        //unset equipments
        unset($rental['equipments']);

        //Supprimer id et rental de $equipments
        unset($equipments['id']);
        unset($equipments['rental']);

        //Ajouter les equipments dans le $rental
        $rental['equipments'] = $equipments;


        // Récupérer les saisons associées via les prix
        foreach ($reservation->getRental()->getPrices() as $price) {
            $season = $price->getSeason();
            $rental['seasons'][] = [
                'label' => $season->getLabel(),
                'season_start' => $season->getSeasonStart(),
                'season_end' => $season->getSeasonEnd(),
                'pricePerNight' => $price->getPricePerNight(),
            ];
        }


        return $this->render('client/reservation/show.html.twig', [
            'reservation' => $reservation,
            'rental' => $rental,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientReservationType::class, $reservation, ['is_edit' => true]);
        $form->handleRequest($request);

        // Récupération du rental
        $rentalId = $reservation->getRental()->getId();
        $rental = $entityManager->getRepository(Rental::class)->findRentalInfoById($rentalId);
        if (!$rental) {
            throw $this->createNotFoundException('Location non trouvée.');
        }

        // Traitement du rental
        $seasons = [];
        foreach ($rental as $rental) {
            $seasons[] = [
                'label' => $rental['season_label'],
                'date_start' => $rental['season_start'],
                'date_end' => $rental['season_end'],
                'pricePerNight' => $rental['pricePerNight']
            ];
        }

        //unset season_label, season_start, season_end
        $seasons = array_map("unserialize", array_unique(array_map("serialize", $seasons)));

        //Initialiser la variable $calculatedPrice
        $calculatedPrice = null;

        if ($form->isSubmitted()) {
            if ($request->request->get('action') === 'calculate') {

                $dateStart = $reservation->getDateStart();
                $dateEnd = $reservation->getDateEnd();

                //vérification des dates de début et de fin
                if ($dateStart >= $dateEnd) {
                    $this->addFlash('danger', 'La date de début doit être inférieure à la date de fin.');
                    return $this->redirectToRoute('app_admin_reservation_edit', ['id' => $reservation->getId()]);
                }

                if ($dateStart < new \DateTime()) {
                    $this->addFlash('danger', 'La date de début doit être supérieure à la date actuelle.');
                    return $this->redirectToRoute('app_admin_reservation_edit', ['id' => $reservation->getId()]);
                }

                // Vérifiez que les dates ne sont pas nulles
                if ($dateStart && $dateEnd) {
                    $calculatedPrice = $this->calculatePrice($dateStart, $dateEnd, $seasons);
                } else {
                    $this->addFlash('danger', 'Veuillez entrer des dates valides.');
                }

                if ($dateStart >= $dateEnd) {
                    $this->addFlash('danger', 'La date de début doit être inférieure à la date de fin.');
                    return $this->redirectToRoute('app_admin_reservation_edit', ['id' => $reservation->getId()]);
                }

                if ($dateStart < new \DateTime()) {
                    $this->addFlash('danger', 'La date de début doit être supérieure à la date actuelle.');
                    return $this->redirectToRoute('app_admin_reservation_edit', ['id' => $reservation->getId()]);
                } else {
                    $this->addFlash('danger', 'Veuillez entrer des dates valides.');
                }
            } elseif ($form->isValid()) {
                //Prendre les dates soumises par l'utilisateur et appeler la methode calculatePrice pour obtenir le prix total et le stocker dans la bdd sous applied_price_total
                $calculatedPrice = $this->calculatePrice($reservation->getDateStart(), $reservation->getDateEnd(), $seasons);
                $reservation->setAppliedPriceTotal($calculatedPrice);

                //Set quand même le renter pour éviter les injections
                $reservation->setRenter($this->getUser());

                $entityManager->flush();

                return $this->redirectToRoute('app_client_reservation_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('client/reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
            'calculatedPrice' => $calculatedPrice,
        ]);
    }


}
