<?php

namespace App\Form;

use App\Entity\CourseCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Import TextType to define a text input field.
use Symfony\Component\Validator\Constraints as Assert;

class CourseCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The category name field should not be blank.',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'The category name should be at least {{ limit }} characters long.',
                        'max' => 255,
                        'maxMessage' => 'The name cannot exceed {{ limit }} characters.',
                    ]),
                ],
                'label' => 'Course category name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseCategory::class,
        ]);
    }
}
