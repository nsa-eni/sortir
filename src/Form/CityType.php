<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label"=>"Ville",
                "trim"=>true,
                "constraints" => [
                    new NotBlank([
                        "message"=>"Le champ Ville ne peut pas être vide !"
                    ])
                ]])
            ->add('zipCode', TextType::class, [
                "label"=>"Code postal",
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

