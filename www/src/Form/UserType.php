<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('email');
            //Password.
            if (!$options['is_edit']) {
                $builder->add('password', PasswordType::class, [
                    'label' => 'Mot de passe',
                    'mapped' => false,
                    'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe',
                        ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'le mot de passe doit contenir au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ]);
            }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => 'Rôles',
                'attr' => ['class' => 'form-control'],
            ]);
        }
        $builder
            ->add('firstname',
                null,
                [
                    'label' => 'Prénom',
                ])
            ->add('lastname', null, [
                'label' => 'Nom',
            ])
            ->add('dateOfBirth', null, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
            ])
            ->add('username', null, 
                [
                    'label' => 'Nom d\'utilisateur',
                ])
            ->add('phone', null, 
                [
                    'required' => false,
                    'label' => 'Téléphone',
                ])
            ->add('address', null, 
                [
                    'required' => false,
                    'label' => 'Adresse',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
