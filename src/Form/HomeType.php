<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', TextType::class, ['label' => 'Site :', 'required' => false])
            ->add('name', TextType::class, ['label' => 'Le nom de la sortie contient :', 'required' => false])
            ->add('date_start', DateTimeType::class,
                ['label' => 'Entre', 'widget' => 'single_text', 'required' => false])
            ->add('date_end_of_registration', DateTimeType::class
                , ['label' => 'et', 'widget' => 'single_text', 'required' => false])
            ->add('user', CheckboxType::class, ['label' => 'Sorties dont je suis l\'organisateur', 'required' => false])
            //->add('subscribed', CheckboxType::class, ['label' => 'Sorties auxquelles je suis inscrit/e', 'required' => false])
            //->add('notSubscribed', CheckboxType::class, ['label' => 'Sorties auxquelles je ne suis pas inscrit/e', 'required' => false])
            ->add('eventEnded', CheckboxType::class, ['label' => 'Sorties passées', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
