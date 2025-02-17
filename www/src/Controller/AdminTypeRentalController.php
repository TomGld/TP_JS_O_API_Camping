<?php

namespace App\Controller;

use App\Entity\TypeRental;
use App\Form\TypeRentalType;
use App\Repository\TypeRentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/type/rental')]
final class AdminTypeRentalController extends AbstractController
{
    #[Route(name: 'app_type_rental_index', methods: ['GET'])]
    public function index(TypeRentalRepository $typeRentalRepository): Response
    {
        return $this->render('admin/type_rental/index.html.twig', [
            'type_rentals' => $typeRentalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_rental_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeRental = new TypeRental();
        $form = $this->createForm(TypeRentalType::class, $typeRental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeRental);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_rental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/type_rental/new.html.twig', [
            'type_rental' => $typeRental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_rental_show', methods: ['GET'])]
    public function show(TypeRental $typeRental): Response
    {
        return $this->render('admin/type_rental/show.html.twig', [
            'type_rental' => $typeRental,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_rental_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeRental $typeRental, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeRentalType::class, $typeRental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_rental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/type_rental/edit.html.twig', [
            'type_rental' => $typeRental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_rental_delete', methods: ['POST'])]
    public function delete(Request $request, TypeRental $typeRental, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeRental->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typeRental);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_rental_index', [], Response::HTTP_SEE_OTHER);
    }
}
