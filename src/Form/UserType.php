<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'app.username',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'app.password.invalid.message',
                'required' => true,
                'first_options' => ['label' => 'app.password.first'],
                'second_options' => ['label' => 'app.password.second'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.email'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'app.user' => 'ROLE_USER',
                    'app.admin' => 'ROLE_ADMIN',
                ],
                'label' => 'app.roles',
                'mapped' => false,
            ])
        ;
    }
}
