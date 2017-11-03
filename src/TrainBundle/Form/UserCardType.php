<?php

namespace TrainBundle\Form;

use TrainBundle\Entity\UserCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UserCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('discount_card',EntityType::class, array(
            'class' => 'TrainBundle:DiscountCard',
            'choice_label' => 'name'))
            ->add('discount_card_number', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Ajouter la carte'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserCard::class,
        ));
    }

}