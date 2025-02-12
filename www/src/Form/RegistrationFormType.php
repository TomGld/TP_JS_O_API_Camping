<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
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
            ],
            ])
            ->add('firstname', TextType::class, [
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
            'constraints' => [
                new NotBlank([
                'message' => 'Merci d\'entrer votre date de naissance',
                ]),
            ],
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            ])
            ->add('username', TextType::class, [
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
