<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Create password'],
                'second_options' => ['label' => 'Verify password'],
                'invalid_message' => 'Passwords did not match'
            ))
            ->add('strength', HiddenType::class, array('data'=>0));
    }
}