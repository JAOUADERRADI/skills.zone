<?php

namespace App\Form;

use App\Entity\CourseCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

class SearchCourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keyword', SearchType::class, [
                'label' => 'Search by name',
                'required' => false,
                'attr' => ['placeholder' => 'Enter course name']
            ])
            ->add('category', EntityType::class, [
                'class' => CourseCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a category',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
