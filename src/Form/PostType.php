<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostCategory;
use App\Entity\User;
use App\Enum\PostStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;


class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => 225
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The content should not be blank.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, ([
                'label' => 'Short Description',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The description should not be blank.',
                    ]),
                    new Assert\Length(['max' => 500]),
                ],
            ]))
            ->add('image', FileType::class, [
                'label' => 'Upload Image',
                'required' => false,
                'constraints' => [
                    new Assert\Image([
                        'maxSize' => '10M',
                    ]),
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Draft' => PostStatus::Draft,
                    'Published' => PostStatus::Published,
                    'Archived' => PostStatus::Archived,
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => PostCategory::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
