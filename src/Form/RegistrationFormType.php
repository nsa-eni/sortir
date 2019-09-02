<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
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
            ->add('pseudo', TextType::class, [
                "label" => "Pseudo",
                "required" => false,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Pseudo ne peut pas être vide !"
                    ]),
                    new Length([
                        "max"=>"30",
                        "maxMessage"=>"Le pseudo ne peut faire plus de 30 caractères !"
                    ])
            ]])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "required" => true,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Email ne peut pas être vide !"
                    ]),
                    new Length([
                        "max"=>"180",
                        "maxMessage"=>"L'email ne peut faire plus de 180 caractères !"
                    ]),
                    new Email([
                        "message"=>"Email non valide !"
                    ])
            ]])
            ->add('name', TextType::class, [
                "label" => "Nom",
                "required" => false,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Nom ne peut pas être vide !"
                    ]),
                    new Length([
                        "max"=>"100",
                        "maxMessage"=>"Le nom ne peut faire plus de 100 caractères !"
                    ])
            ]])
            ->add('firstname', TextType::class, [
                "label" => "Prénom",
                "required" => false,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Prénom ne peut pas être vide !"
                    ]),
                    new Length([
                        "max"=>"100",
                        "maxMessage"=>"Le prénom ne peut faire plus de 100 caractères !"
                    ])
                ]])
            ->add('phone', TextType::class, [
                "label"=>"Téléphone",
                "required"=>false,
                "trim"=>true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Le champ Téléphone ne peut pas être vide !"
                    ]),
                    new Regex(["pattern" => "/^(0)[1-9][0-9]{8}$/",
                        "message" => "Le format est 02 00 00 00 00!"])
                ]])
            ->add('password', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' =>'Les 2 champs de mot de passe doivent être identiques !',
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                "trim"=>true,
                'constraints'=>
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au minimum 6 caractères !',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
            ])
            ->add('imageFilename', FileType::class, [
                'label' => 'Ma photo',

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
                        'mimeTypesMessage' => 'Veuillez transmettre un fichier image !',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpg',
                            'image/bmp',
                            'image/png',
                            'image/jpeg'
                        ]
                    ])
                ],
            ])
            ->add("submit", SubmitType::class,[
                "label" => "Enregistrer"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
