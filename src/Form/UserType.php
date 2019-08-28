<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo',TextType::class)
            ->add('firstname',TextType::class)
            ->add('name',TextType::class)
            ->add('phone',NumberType::class, [
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Ville ne peut pas être vide !"
                    ]),
                    new Regex(["pattern"=> " \^(\d\d\s){4}(\d\d)$\ ",
                        "message"=>"format non valide !"])

                ]])
            ->add('email',EmailType::class, [
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ email ne peut pas être vide !"
                    ]),
                    new Regex(["pattern"=>" /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ ",
                        "message"=>"Code postal au format 01000 !"])

                ]])
            ->add('password',PasswordType::class)
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
