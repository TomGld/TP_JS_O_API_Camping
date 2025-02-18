<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //Dédie l'accès à l'admin

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //Dédie l'accès à l'admin


        $user = new User();
        $form = $this->createForm(UserType::class,$user, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on récupère la valeur de plainPassword du formulaire
            $plainPassword = $form->get('password')->getData();
            //on encode le mdp pour le set dans $user
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            //Récupération des rôles
            $roles = $form->get('roles')->getData();
            //si le tableau contient ROLE_ADMIN et ROLE_USER , on retourne un tableau avec uniquement ROLE_ADMIN
            if (in_array('ROLE_ADMIN', $roles) && in_array('ROLE_USER', $roles)) {
                $roles = ['ROLE_ADMIN'];
            }
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {

        // Vérifie si l'utilisateur connecté est le propriétaire du profil ou un administrateur
        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }


        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        // Vérifie si l'utilisateur connecté est le propriétaire du profil ou un administrateur
        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }


        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des rôles uniquement si l'utilisateur est un administrateur
            if ($this->isGranted('ROLE_ADMIN')) {
                $roles = $form->get('roles')->getData();
                // Si le tableau contient ROLE_ADMIN et ROLE_USER, on retourne un tableau avec uniquement ROLE_ADMIN
                if (in_array('ROLE_ADMIN', $roles) && in_array('ROLE_USER', $roles)) {
                    $roles = ['ROLE_ADMIN'];
                }
                // Réindexer le tableau des rôles
                $user->setRoles(array_values($roles));
            }
            //Si ROLE_USER, on ajoute ROLE_USER
            else {
                $user->setRoles(['ROLE_USER']);
            }


            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN'); //Dédie l'accès à l'admin

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
