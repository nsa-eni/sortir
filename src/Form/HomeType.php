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
            ->add('city', TextType::class, ['label' => 'Site :'])
            ->add('search', TextType::class, ['label' => 'Le nom de la sortie contient :'])
            ->add('date_start', DateTimeType::class,
                ['label' => 'Entre', 'widget' => 'single_text'])
            ->add('date_end', DateTimeType::class
                , ['label' => 'et', 'widget' => 'single_text'])
            ->add('owner', CheckboxType::class, ['label' => 'Sorties dont je suis l\'organisateur'])
            ->add('subscribed', CheckboxType::class, ['label' => 'Sorties auxquelles je suis inscrit/e'])
            ->add('notSubscribed', CheckboxType::class, ['label' => 'Sorties auxquelles je ne suis pas inscrit/e'])
            ->add('eventEnded', CheckboxType::class, ['label' => 'Sorties passées'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
