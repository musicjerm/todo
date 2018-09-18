<?php

namespace App\Form\Task;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TaskCreateType extends AbstractType
{
    /** @var User */
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        if ($tokenStorage->getToken() !== null){
            $this->user = $tokenStorage->getToken()->getUser();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, array(
                'required' => false
            ))
            ->add('userGroups', EntityType::class, array(
                'class' => \App\Entity\UserGroup::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('ug')
                        ->where('ug.userCreated = :user')
                        ->setParameter('user', $this->user);
                },
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'select2', 'style' => 'width: 100%']
            ))
            ->add('priority', ChoiceType::class, array(
                'choices' => array(
                    'Minor' => 'Minor',
                    'Major' => 'Major',
                    'Critical' => 'Critical'
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
        $resolver->setDefaults(array(
            'data_class' => TaskData::class,
        ));
    }
}