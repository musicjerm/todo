<?php

namespace App\Form\Task;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, array(
                'required' => false
            ))
            ->add('followUp', TextareaType::class, array(
                'required' => false
            ))
            ->add('priority', ChoiceType::class, array(
                'choices' => array(
                    'Minor' => 'Minor',
                    'Major' => 'Major',
                    'Critical' => 'Critical'
                )
            ))
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'Open' => 'Open',
                    'Work in Progress' => 'Work in Progress',
                    'Closed' => 'Closed',
                    'On Hold' => 'On Hold',
                    'Cancelled' => 'Cancelled'
                )
            ))
            ->add('public', CheckboxType::class, ['required' => false])
            ->add('targetCompleteDate', DateType::class, array(
                'label' => 'Target Completion Date',
                'required' => false,
                'html5' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker']
            ))
            ->add('tags', null, array(
                'label' => 'Tags / Categories (Comma Separated)'
            ))
            ->add('document');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', TaskData::class);
    }
}