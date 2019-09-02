<?php

namespace App\Form;

use App\Entity\Site;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RegistrationByImportFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'label' => "Site de rattachement",
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s');
                },
                'choice_label' => 'name',
            ])
            ->add('file', FileType::class,
                [
                    'required' => true,
                    'label' => 'Ficher (CSV file)',
                    'constraints' => [
                        new File([
                            'maxSize' => '9024k',
                            'mimeTypes' => [
                                'text/csv',
                                'text/csv-schema',
                                'application/octet-stream',
                                'text/plain'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid CSV document',
                        ])
                    ],
                ])
            ->add('submit', SubmitType::class, [
                'label' => 'Importer et enregister'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
