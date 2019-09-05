<?php


namespace App\Form;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom",
                "required" => false,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Le champ Nom ne peut pas être vide !"
                    ]),
                    new Length([
                        "max" => "50",
                        "maxMessage" => "Le nom ne peut faire plus de 50 caractères !"
                    ])
                ]])
            ->add('firstname', TextType::class, [
                "label" => "Prénom",
                "required" => false,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Le champ Prénom ne peut pas être vide !"
                    ]),
                    new Length([
                        "max" => "50",
                        "maxMessage" => "Le prénom ne peut faire plus de 50 caractères !"
                    ])
                ]])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "required" => true,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Le champ Email ne peut pas être vide !"
                    ]),
                    new Length([
                        "max" => "180",
                        "maxMessage" => "L'email ne peut faire plus de 180 caractères !"
                    ]),
                    new Email([
                        "message" => "Email non valide !"
                    ])
                ]])
            ->add('phone', TextType::class, [
                "label" => "Téléphone",
                "required" => false,
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Le champ Téléphone ne peut pas être vide !"
                    ]),
                    new Regex(["pattern" => "/^(0)[1-9][0-9]{8}$/",
                        "message" => "Le format d'un numéro de téléphone est 02 00 00 00 00!"])
                ]])
            ->add('message', TextareaType::class, [
                "label" => "Message",
                "trim" => true,
                "constraints" => [
                    new NotBlank([
                        "message" => "Le champ Téléphone ne peut pas être vide !"
                    ]),
                    new Length([
                        "max" => "1000",
                        "maxMessage" => "Le message ne peut faire plus de 1000 caractères !"
                    ]),
                ]])
            ->add("submit", SubmitType::class, [
                "label" => "Envoyer le mail"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
