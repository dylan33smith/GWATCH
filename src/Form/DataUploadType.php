<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class DataUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('moduleName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a module name',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Module name should be at least {{ limit }} characters',
                        'max' => 100,
                        'maxMessage' => 'Module name should not exceed {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter module name (e.g., Module186)',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Description should not exceed {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter module description (optional)',
                    'class' => 'form-control',
                    'rows' => 3
                ]
            ])
            ->add('makePublic', CheckboxType::class, [
                'required' => false,
                'label' => 'Make this dataset public',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            // TODO: Add file upload fields here step by step for testing
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
