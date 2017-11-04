<?php

namespace TrainBundle\Form;

use TrainBundle\Entity\Trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use TrainBundle\Entity\Station;

use Symfony\Component\Routing\Router;


class TripType extends AbstractType
{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('departure_station_id',Select2EntityType::class, array(
                'multiple' => false,
                'remote_path' => $this->router->generate("search_station"),
                'class' => Station::class,
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 2,
                'page_limit' => 10,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => false,
                'cache_timeout' => 1,
                'language' => 'fr',
                'placeholder' => 'Rechercher une gare de départ',
            ))
            ->add('arrival_station_id',Select2EntityType::class, array(
                'multiple' => false,
                'remote_path' => $this->router->generate("search_station"),
                'class' => Station::class,
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 2,
                'page_limit' => 10,
                'allow_clear' => true,
                'cache' => false,
                'cache_timeout' => 1,
                'delay' => 250,
                'language' => 'fr',
                'placeholder' => 'Rechercher une gare d\'arrivée',
            ))
            ->add('from_departure_date', DateTimeType::class, array(
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'widget' => 'single_text',
                'attr' => array('class' => "datetimepicker"),
                'html5' => false))
            ->add('to_departure_date', DateTimeType::class, array(
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'widget' => 'single_text',
                'attr' => array('class' => "datetimepicker"),
                'html5' => false))
            ->add('booking', CheckboxType::class, array(
                'label' => 'Auto reservation ?',
                'required' => false
            ))
            /*->add('notification',CheckboxType::class, array(
                'label' => 'Notification de disponibilité ?',
                'required' => false
            ))*/
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Trip::class,
        ));
    }

}