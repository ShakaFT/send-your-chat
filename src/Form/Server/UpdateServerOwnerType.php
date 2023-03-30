<?php

namespace App\Form\Server;

use App\DTO\Server\UpdateServerOwnerDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateServerOwnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newOwner', TextType::class, [
				'label' => "Pseudo du nouveau propriétaire",
                'attr' => [
                    'class' => 'form-control',
                ],
			]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdateServerOwnerDto::class,
        ]);
    }
}
