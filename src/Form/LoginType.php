<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your username',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter username',
                    'class' => 'form-control'
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your password',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter password',
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
