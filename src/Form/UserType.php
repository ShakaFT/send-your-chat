<?php

namespace App\Form;

use App\DTO\UserDto;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserType extends AbstractType
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {        
        $getToken = $this->token->getToken();

        /** @var User $user */
        $user = $getToken ? $getToken->getUser() : null;
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'data' => $user ? $user->getUsername() : "",
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'data' => $user ? $user->getEmail() : "",
                'attr' => [
                    'class' => 'form-control',
                ],

            ]);
        if (!$user) {
            $builder
                ->add('password', PasswordType::class, [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
                ->add('passwordConfirm', PasswordType::class, [
                    'label' => 'Confirmation du mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDto::class,
        ]);
    }
}
