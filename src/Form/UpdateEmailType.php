<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

// Import the Assert component from Symfony's Validator, allowing the use of various validation constraints.
use Symfony\Component\Validator\Constraints as Assert;

class UpdateEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a new email.'
                    ]),
                    new Assert\Email([
                        'message' => 'Please enter a valid email address.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
