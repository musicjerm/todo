<?php

namespace App\Form\CRUD;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email', EmailType::class, array(
                'attr'=>array(
                    'class' => 'inputmask',
                    'data-inputmask' => "'alias': 'email'"
                ),))
            ->add('password', PasswordType::class, ['required' => false])
            ->add('firstName')
            ->add('lastName')
            ->add('roles', ChoiceType::class, array(
                'multiple' => false,
                'expanded' => true,
                'choices' => array(
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN'
                )
            ))
            ->add('isActive', CheckboxType::class, ['label' => 'Active', 'required' => false]);
    }
}