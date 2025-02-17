<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/season')]
final class AdminSeasonController extends AbstractController
{
    #[Route(name: 'app_season_index', methods: ['GET'])]
    public function index(SeasonRepository $seasonRepository): Response
    {
        return $this->render('admin/season/index.html.twig', [
            'seasons' => $seasonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_season_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($season->getSeasonStart() > $season->getSeasonEnd()) {
                $this->addFlash('error', 'La date de départ doit être inférieure à la date de fin.');
            } else {
                $entityManager->persist($season);
                $entityManager->flush();

                return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin/season/new.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_season_show', methods: ['GET'])]
    public function show(Season $season): Response
    {
        return $this->render('admin/season/show.html.twig', [
            'season' => $season,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_season_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($season->getSeasonStart() > $season->getSeasonEnd()) {
                $this->addFlash('error', 'La date de départ doit être inférieure à la date de fin.');
            } else {
                $entityManager->flush();

                return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin/season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_season_delete', methods: ['POST'])]
    public function delete(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($season);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
    }
}
