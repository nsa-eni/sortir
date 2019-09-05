<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Location;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('date_start', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie',
                'data' => new \DateTime('now')
            ])
            ->add('dateEndOfRegistration', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription'

            ])
            ->add('time_delay_minutes', TextType::class, [
                'label' => 'Durée'
            ])
            ->add('max_number_places', TextType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('info', TextareaType::class, [
                'label' => 'Description et info'
            ])
            ->add('location', LocationType::class)
        ;
        $builder->add('save', SubmitType::class, [
            'label' => 'Enregistrez'
        ]);

        $builder->add('cancel', SubmitType::class,[
            'label' => 'Annuler'
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
