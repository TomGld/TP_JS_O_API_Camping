<?php

namespace App\Form;

use App\Entity\Rental;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['is_edit'] === false) {
            $builder
                ->add('dateStart', null, [
                    'widget' => 'single_text',
                ])
                ->add('dateEnd', null, [
                    'widget' => 'single_text',
                ])
                ->add('nbrAdult')
                ->add('nbrMinor')
            ;
        } else {
            $builder
                ->add('status', ChoiceType::class, [
                    'label' => 'Pour annuler la réservation, cochez cette case, puis cliquez sur Modifier.',
                    'choices' => [
                        'Annuler' => '2',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'help' => 'Attention, cette action est irréversible.',
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'is_edit' => false,
        ]);
    }
}
