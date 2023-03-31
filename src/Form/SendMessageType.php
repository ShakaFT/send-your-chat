<?php

namespace App\Form;

use App\DTO\SendMessageDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SendMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'placeholder' => 'Envoyer un message...',
                    'autofocus' => true,
                    'class' => 'form-control rounded-pill py-3 bg-light border-0 shadow-sm px-4',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SendMessageDto::class,
        ]);
    }
}
