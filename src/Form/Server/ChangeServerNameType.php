<?php

namespace App\Form\Server;

use App\DTO\Server\ChangeServerNameDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeServerNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serverName', TextType::class, [
				'label' => "Nouveau nom du serveur"
			]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangeServerNameDto::class,
        ]);
    }
}
