<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class LoginFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'username',
                'label_attr' => [
                    'class' => 'sr-only'
                ]
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'password',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'maxlength' => PasswordHasherInterface::MAX_PASSWORD_LENGTH
                ]
            ])
            ->add('_csrf_token', HiddenType::class)
            ->add('_target_path', HiddenType::class)
            ->add('_submit', SubmitType::class, [
                'label' => 'login'
            ]);
    }
}
