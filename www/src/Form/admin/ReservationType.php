<?php

namespace App\Form;

use App\Entity\Price;
use App\Entity\Rental;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateStart', null, [
                'label' => 'Date de début : ',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateEnd', null, [
                'label' => 'Date de fin : ',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('nbrAdult', null, [
                'label' => 'Nombre d\'adultes : ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('nbrMinor', null, [
                'label' => 'Nombre d\'enfants : ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('status', ChoiceType::class , [
                'label' => 'Statut de la réservation : ',
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    'Validée' => 1,
                    'Annulée' => 2,
                    'En attente' => 3,
                ],
            ])
            ->add('checked', ChoiceType::class, [
                'label' => 'Location vérifiée : ',
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    'Oui' => 1,
                    'Non' => 2,
                ],
            ])
            ->add('renter', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user) {
                    return $user->getFirstname() . ' ' . $user->getLastname() . ', user N°' . $user->getId();
                },
                'attr' => ['class' => 'form-control'],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
