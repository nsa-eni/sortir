<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label"=>"Ville",
                "required"=>false,
                "trim"=>true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Ville ne peut pas être vide !"
                    ]),
                    new Length([
                        "max"=>"30",
                        "maxMessage"=>"Le nom ne peut faire plus de 30 caractères !"
                    ]),
                ]])
            ->add('zipCode', TextType::class, [
                "label"=>"Code postal",
                "required"=>false,
                "trim"=>true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Code postal ne peut pas être vide !"
                    ]),
                    new Regex(["pattern"=>"/[0-9]{5}/",
                        "message"=>"Code postal au format 01000 !"])
                ]])
            ->add("submit", submitType::class,[
                "label" => "Ajouter"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
}

