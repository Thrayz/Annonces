<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Username')
            ->add('Email')
            ->add('UserType')
            ->add('password', RepeatedType::class, [
                     'type' => PasswordType::class,
                     'invalid_message' => 'The password fields must match.',
                     'options' => ['attr' => ['class' => 'password-field']],
                     'required' => true,
                     'first_options'  => ['label' => 'Password'],
                     'second_options' => ['label' => 'Repeat Password'],
                 ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
