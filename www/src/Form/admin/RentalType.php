<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Rental;
use App\Entity\TypeRental;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class RentalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
            'label' => 'Titre',
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'Le titre est obligatoire',
            ]),
            ],
            ])
            ->add('description', null, [
            'label' => 'Description',
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'La description est obligatoire',
            ]),
            ],
            ])
            ->add('capacity', null, [
            'label' => 'Capacité',
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'La capacité est obligatoire',
            ]),
            ],
            ])
            ->add('nbrLocalization', null, [
            'label' => 'Nombre de localisation',
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'Le nombre de localisation est obligatoire',
            ]),
            ],
            ])
            ->add('isActive', ChoiceType::class, [
            'label' => 'En ligne',
            'choices' => [
            'Oui' => 1,
            'Non' => 0,
            ],
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'Le statut en ligne est obligatoire',
            ]),
            ],
            ])
            ->add('image', FileType::class, [
            'label' => 'Image',
            'mapped' => false,
            'required' => false,
            'constraints' => [
            new File([
                'maxSize' => '10000k',
                'mimeTypes' => [
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/gif',
                'image/webp'
                ],
                'mimeTypesMessage' => 'Merci de choisir un format d\'image valide'
            ])
            ],
            ])
            ->add('typeRental', EntityType::class, [
            'class' => TypeRental::class,
            'choice_label' => 'label',
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'Le type de location est obligatoire',
            ]),
            ],
            ])
            ->add('equipments', EntityType::class, [
            'class' => Equipment::class,
            'choice_label' => 'label',
            'multiple' => true,
            'expanded' => true, // This will render checkboxes
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rental::class,
        ]);
    }
}
