<?php

namespace App\Form\CRUD;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
                'required' => false
            ))
            ->add('document');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => \App\Form\DTO\TaskData::class,
        ));
    }
}