<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Import necessary Symfony form types for creating a registration form.
use Symfony\Component\Form\Extension\Core\Type\EmailType;    // Used to create an input field for email addresses.
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; // Used for creating a checkbox input
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Creates an input field for passwords, ensuring that the text is masked for security
use Symfony\Component\Form\Extension\Core\Type\RepeatedType; // Used when a field (such as a password) must be entered twice to ensure consistency
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;    // Used for creating a select input for roles

// Import the Assert component from Symfony's Validator, allowing the use of various validation constraints.
use Symfony\Component\Validator\Constraints as Assert;

// use Symfony\Component\Validator\Constraints\IsTrue;
// use Symfony\Component\Validator\Constraints\Length;
// use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'The email field should not be blank.'
                    ]),
                    new Assert\Email([
                        'message' => 'Please enter a valid email address.'
                    ])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new Assert\NotCompromisedPassword([
                            'message' => 'This password has been exposed in a data breach, it cannot be used. Please use another password.',
                        ]),
                    ],
                    'label' => 'Password',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'Repeat Password',
                ],
                'invalid_message' => 'The password fields must match.',
            ])
            // ->add('roles', ChoiceType::class, [
            //     'choices'  => [
            //         'User' => 'ROLE_USER',
            //         'Admin' => 'ROLE_ADMIN',
            //     ],
            //     'expanded' => false,
            //     'multiple' => true,
            //     'label' => 'Roles'
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
