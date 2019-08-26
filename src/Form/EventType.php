<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('date_start', DateTimeType::class, [
                'widget' => 'single_text',

            ])
            ->add('dateEndOfRegistration', DateTimeType::class, [
                'widget' => 'single_text',

            ])
            ->add('time_delay_minutes')
            ->add('max_number_places')
            ->add('info', TextareaType::class)
        ;
        $builder->add('save', SubmitType::class);
        $builder->add('publish', SubmitType::class);
        $builder->add('cancel', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
