<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur :',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'johndoe']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email :',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'johndoe@gmail.com']
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe :', 'attr' => ['class' => 'form-control', 'placeholder' => '*******']],
                'second_options' => ['label' => 'Confirmer le mot de passe :', 'attr' => ['class' => 'form-control', 'placeholder' => '*******']],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => '8 rue du bac, 54100 Nancy']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
