<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => "Nom complet",
                'attr' => [
                    'placeholder' => "Votre nom complet",
                ],
                "constraints" => [
                    new NotBlank(),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Adresse mail",
                'attr' => [
                    'placeholder' => "Votre adresse mail"
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                "invalid_message" => "Les deux mots de passe doivent correspondre !",
                'first_options' => [
                    'label' => "Mot de passe",
                    "attr" => [
                        'placeholder' => "Votre mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation',
                    'attr' => [
                        'placeholder' => "Répétez le mot de passe"
                    ]
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
