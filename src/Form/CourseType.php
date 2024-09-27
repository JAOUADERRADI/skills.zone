<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;


class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Course Title',
                'attr' => [
                    'placeholder' => 'Enter the course title',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The title should not be blank.',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'The title cannot be longer than {{ limit }} characters.',
                    ]),
                    // new Assert\Regex([
                    //     'pattern' => '/^[A-Za-z0-9\s\-]+$/',
                    //     'message' => 'The title can only contain letters, numbers, spaces, and hyphens.',
                    // ]),
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Course Description',
                'attr' => [
                    'placeholder' => 'Enter the course title',
                ],
                'constraints' => [
                    new Assert\NotBlank ([
                        'message' => 'The description course should not be blank.',
                    ]),
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Course Image (JPG, PNG file)',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*'],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG or PNG).',
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => CourseCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a category',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please select a category.',
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
