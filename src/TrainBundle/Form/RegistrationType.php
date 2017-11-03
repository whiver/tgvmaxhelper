<?php

namespace TrainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use TrainBundle\Entity\User;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gender', ChoiceType::class, array(
            'choices'   => array('M.' => 'M.', 'Mme' => 'Mme')))
                ->add('name')
                ->add('surname')
                ->add('birthdate', BirthdayType::class, array(
                    'format' => 'dd/MM/yyyy',
                ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }



}