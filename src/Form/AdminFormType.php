<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\Admin;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class AdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', TextType::class, [
            'label' => 'Felhasználónév',
            'constraints' => [
                new NotBlank([
                    'message' => 'Hiba! Kérjük töltsd ki a Név mezőt!',
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9_-éáűúőóüöíÉÁŰÚŐÓÜÖÍ]+$/u',
                    'message' => 'Hiba! Kérjük ne használj speciális karaktereket, ill. szüneteket!',
                ])
            ],
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Jelszó',
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Hiba! Kérjük töltsd ki a Jelszó mezőt!',
                ])
            ],
        ])
        ->add('button', SubmitType::class, [
            'label' => 'Mentés',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
