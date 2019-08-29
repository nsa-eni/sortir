<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => function($site) {
                    return $site->getName();
                },
                'required' => false
            ])
            ->add('name', TextType::class, ['label' => 'Le nom de la sortie contient :', 'required' => false])
            ->add('date_start', DateTimeType::class,
                ['label' => 'Entre', 'widget' => 'single_text', 'required' => false])
            ->add('date_end_of_registration', DateTimeType::class
                , ['label' => 'et', 'widget' => 'single_text', 'required' => false])
            ->add('user', CheckboxType::class, ['label' => 'Sorties dont je suis l\'organisateur', 'required' => false])
            ->add('subscribed', CheckboxType::class, ['label' => 'Sorties auxquelles je suis inscrit/e', 'required' => false])
            ->add('notSubscribed', CheckboxType::class, ['label' => 'Sorties auxquelles je ne suis pas inscrit/e', 'required' => false])
            ->add('eventEnded', CheckboxType::class, ['label' => 'Sorties passées', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
