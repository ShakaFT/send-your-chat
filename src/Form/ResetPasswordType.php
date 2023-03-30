<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('oldPassword', PasswordType::class, [
            'attr' => [
                'class' => 'form-control',
            ],
            'label' => 'Ancien Mot de passe'
        ])
        ->add('newPassword', PasswordType::class, [
            'label' => 'Nouveau mot de passe',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('confirmPassword', PasswordType::class, [
            'attr' => [
                'class' => 'form-control',
            ],
            'label' => 'Confirmation du mot de passe'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
