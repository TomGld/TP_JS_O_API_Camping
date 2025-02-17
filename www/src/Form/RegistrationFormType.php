<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Adresse email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre adresse email',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Votre adresse email doit comporter au maximum {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/',
                        'message' => 'Merci d\'entrer une adresse email valide',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au minimum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[0-9])(?=.*[\W]).+$/', // Au moins un chiffre et un caractère spécial
                        'message' => 'Votre mot de passe doit comporter au minimum un caractère spécial et un chiffre',
                    ]),
                    ]
                ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre prénom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit comporter au minimum {{ limit }} caractères',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit comporter au minimum {{ limit }} caractères',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('date_of_birth', DateType::class, [
                'label' => 'Date de naissance',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre date de naissance',
                    ]),
                ],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer votre nom d\'utilisateur',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom d\'utilisateur doit comporter au minimum {{ limit }} caractères',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre numéro de téléphone doit comporter au minimum {{ limit }} caractères et au maximum {{ limit }}',
                        'max' => 10,
                        'maxMessage' => 'Votre numéro de téléphone doit comporter au minimum {{ limit }} caractères et au maximum {{ limit }}',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre adresse doit comporter au minimum {{ limit }} caractères',
                        'max' => 255,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
