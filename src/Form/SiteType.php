<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label"=>"Ville organisatrice",
                "required"=>false,
                "trim"=>true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Ville ne peut pas être vide !"
                    ]),
                    new Length([
                        "max"=>"30",
                        "maxMessage"=>"Le nom ne peut faire plus de 30 caractères !"
                    ])
                ]])
            ->add("submit", submitType::class,[
                "label" => "Ajouter"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
        ]);
    }
}
