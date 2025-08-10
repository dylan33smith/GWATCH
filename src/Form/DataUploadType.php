<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class DataUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('moduleName', TextType::class, [
                'label' => 'Module Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Module name is required.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Description is required.',
                    ]),
                ],
            ])
            ->add('chrFile', FileType::class, [
                'label' => 'CHR File (CSV)',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'CHR file is required.',
                    ]),
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('chrsuppFile', FileType::class, [
                'label' => 'CHR Support File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('colFile', FileType::class, [
                'label' => 'Column File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('indFile', FileType::class, [
                'label' => 'Index File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('rPvalFile', FileType::class, [
                'label' => 'R P-value File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('rRatioFile', FileType::class, [
                'label' => 'R Ratio File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('vIndFile', FileType::class, [
                'label' => 'Variant Index File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('rowFile', FileType::class, [
                'label' => 'Row Data File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('valFile', FileType::class, [
                'label' => 'Value Data File (CSV)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file.',
                    ]),
                ],
            ])
            ->add('makePublic', CheckboxType::class, [
                'label' => 'Make this module public',
                'required' => false,
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
