<?php

namespace App\Controller;

use App\Repository\RentalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(RentalRepository $rentalRepository): Response
    {
        $title = 'Accueil camping';
        $rentalRepository = $rentalRepository->findAllRentalCard();
        return $this->render('home/index.html.twig', [
            'title' => $title,
            'rentals' => $rentalRepository
        ]);
    }

    #[Route('/detail/{id}', name: 'app_detail')]
    public function projectById(RentalRepository $rentalRepository, int $id): Response
    {
        // On récupère les informations du rental
        $results = $rentalRepository->findRentalInfoById($id);

        // Traitement pour regrouper les informations de chaque saison et prix
        $rentalInfo = [];
        if (!empty($results)) {
            $rentalInfo = [
                'id' => $results[0]['id'],
                'title' => $results[0]['title'],
                'description' => $results[0]['description'],
                'capacity' => $results[0]['capacity'],
                'nbrLocalization' => $results[0]['nbrLocalization'],
                'isActive' => $results[0]['isActive'],
                'image' => $results[0]['image'],
                'type_rental_label' => $results[0]['type_rental_label'],
                'equipments' => [],
                'seasons' => [],
            ];

            foreach ($results as $result) {
                $rentalInfo['equipments'][] = $result['rental_equipment_label'];

                $season = [
                    'season_label' => $result['season_label'],
                    'season_start' => $result['season_start'],
                    'season_end' => $result['season_end'],
                    'pricePerNight' => $result['pricePerNight'],
                ];

                // Vérifier si la saison existe déjà dans le tableau
                if (!in_array($season, $rentalInfo['seasons'])) {
                    $rentalInfo['seasons'][] = $season;
                }
            }

            // Supprimer les doublons dans les équipements
            $rentalInfo['equipments'] = array_unique($rentalInfo['equipments']);
        }



        // On récupère le titre
        $title = $rentalInfo['title'];

        return $this->render('home/detail.html.twig', [
            'rental' => $rentalInfo,
            'title' => $title
        ]);
    }
}
