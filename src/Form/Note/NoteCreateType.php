<?php

namespace App\Form\Note;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entityName', HiddenType::class)
            ->add('entityId', HiddenType::class)
            ->add('comment', TextType::class, array(
                'label' => false,
                'addon' => 'fa-comment',
                'attr' => ['placeholder' => 'Leave a comment']
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Note::class);
    }
}