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
            ->add('csvZipFile', FileType::class, [
                'label' => 'CSV Files ZIP Archive',
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '20G',
                        'mimeTypes' => [
                            'application/zip',
                            'application/x-zip-compressed',
                            'application/octet-stream',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid ZIP file containing your CSV files.',
                    ]),
                ],
                'help' => 'Upload a ZIP file containing all required CSV files. Supports very large archives. For >2GB files, ensure PHP upload limits are configured or use CLI.',
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
