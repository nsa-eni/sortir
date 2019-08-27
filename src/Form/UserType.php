<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('firstname')
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('password')
            ->add('actif')
            ->add('administrator')
         /*  ->add('imageFilename', FileType::class, [
                'label' => 'Image (JPG)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypesMessage'=> 'Veuillez transmettre un fichier image !',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpg',
                            'image/bmp',
                            'image/png',
                        ]
                    ])
                ],
            ])
*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
