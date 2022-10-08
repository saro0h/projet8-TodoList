<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        *   Create conditional Constraints, for new user Adding new NotBlank for password
        */
        $passwordConstraints = [
            new Length(array('min' => 6, 'max' => 16, 'minMessage' => 'Au moins 6 caractères !', 'maxMessage' => 'Maximum 16 caractères !'))
        ];
        if ($options["data"]->getUsername() == '') {
            $passwordConstraints[] = new NotBlank(['message' => 'Le mot de passe ne peux pas être vide']);
        }

        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => array('always_empty' => true),
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
                'constraints' => $passwordConstraints
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Adresse email'
            ])
            ->add('Roles', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
            ]);

        // Data transformer
        $builder->get('Roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
