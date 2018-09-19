<?php

namespace App\Form\CRUD;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('users', EntityType::class, array(
                'class' => \App\Entity\User::class,
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'select2', 'style' => 'width: 100%']
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', \App\Entity\UserGroup::class);
    }
}