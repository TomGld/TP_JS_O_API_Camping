<?php

namespace App\Controller;

use App\Entity\Price;
use App\Entity\Rental;
use App\Form\RentalType;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/rental')]
final class AdminRentalController extends AbstractController
{
    #[Route(name: 'app_admin_rental_index', methods: ['GET'])]
    public function index(RentalRepository $rentalRepository): Response
    {
        return $this->render('admin/rental/index.html.twig', [
            'rentals' => $rentalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_rental_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rental = new Rental();
        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Traitement de l'image
            $image = $form->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename); //Traitement des caractères spéciaux dans le nom du fichier.
                $newFilename = $safeFilename.'_'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('location_images_directory'),
                        $newFilename
                    );
                } catch (FileException) {
                    // ... handle exception if something happens during file upload
                }
                $rental->setImage($newFilename);
                $entityManager->persist($rental);
                $entityManager->flush();
            } else {
                $this->addFlash('error', 'Veuillez insérer une image pour la location.');
                // Ne pas rediriger, juste rendre le formulaire avec les erreurs
                return $this->render('admin/rental/new.html.twig', [
                    'rental' => $rental,
                    'form' => $form,
                ]);
            }

            //Traitement du price
            $pricePerNight = $form->get('pricePerNight')->getData();
            $season = $form->get('season')->getData();

            if ($pricePerNight !== null && $season !== null) {
                $price = new Price();
                $price->setPricePerNight($pricePerNight);
                $price->setSeason($season);
                $price->setRental($rental);

                $entityManager->persist($price);
            }

            $entityManager->persist($rental);
            $entityManager->flush();
            $entityManager->persist($rental);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_rental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rental/new.html.twig', [
            'rental' => $rental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_rental_show', methods: ['GET'])]
    public function show(Rental $rental): Response
    {

        //Reconstruire un tableau associatif pour le component _detail_card.html.twig
        $rentalInfo = [
            'id' => $rental->getId(),
            'type_rental_label' => $rental->getTypeRental()->getLabel(),
            'title' => $rental->getTitle(),
            'description' => $rental->getDescription(),
            'capacity' => $rental->getCapacity(),
            'nbrLocalization' => $rental->getNbrLocalization(),
            'isActive' => $rental->IsActive(),
            'image' => $rental->getImage(),
            'equipments' => $rental->getEquipments()->toArray(),
            'price' => $rental->getPrices(),
            'seasons' => [],
        ];

        //Traitements
            //Traitement pour les equipments
            $equipments = [];
            foreach ($rental->getEquipments() as $equipment) {
                $equipments[] = $equipment->getLabel();
            }

            foreach ($rental->getPrices() as $price) {
                $season = $price->getSeason();
                $rentalInfo['seasons'][] = [
                    'label' => $season->getLabel(),
                    'seasonStart' => $season->getSeasonStart(),
                    'seasonEnd' => $season->getSeasonEnd(),
                    'pricePerNight' => $price->getPricePerNight(),
                ];
            }




        return $this->render('admin/rental/show.html.twig', [
            'rental' => $rentalInfo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_rental_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rental $rental, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
            // Supprimer l'ancienne image
            $oldImage = $rental->getImage();
            if ($oldImage) {
                $oldImagePath = $this->getParameter('location_images_directory').'/'.$oldImage;
                if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
                }
            }

            // Traitement de la nouvelle image
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
            $newFilename = $safeFilename.'_'.uniqid().'.'.$image->guessExtension();
            try {
                $image->move(
                $this->getParameter('location_images_directory'),
                $newFilename
                );
            } catch (FileException) {
                // ... handle exception if something happens during file upload
            }
            $rental->setImage($newFilename);
            }

            //Traitement du price
            $pricePerNight = $form->get('pricePerNight')->getData();
            $season = $form->get('season')->getData();

            if ($pricePerNight !== null && $season !== null) {
                // Vérifier si un prix existe déjà pour cette saison et ce bien
                $price = $entityManager->getRepository(Price::class)->findOneBy([
                    'rental' => $rental,
                    'season' => $season,
                ]);

                if (!$price) {
                    $price = new Price();
                    $price->setRental($rental);
                    $price->setSeason($season);
                }

                $price->setPricePerNight($pricePerNight);
                $entityManager->persist($price);
            }

            $entityManager->persist($rental);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_rental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rental/edit.html.twig', [
            'rental' => $rental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_rental_delete', methods: ['POST'])]
    public function delete(Request $request, Rental $rental, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rental->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rental);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_rental_index', [], Response::HTTP_SEE_OTHER);
    }
}
