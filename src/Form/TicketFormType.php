<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\DTO\TicketFormDTO;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class TicketFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Név',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Hiba! Kérjük töltsd ki a Név mezőt!',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZéáűúőóüöíÉÁŰÚŐÓÜÖÍ ]+$/',
                        'message' => 'Hiba! Kérjük ne használj speciális karaktereket, ill. számokat!',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail cím',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Hiba! Kérjük töltsd ki az E-mail cím mezőt!',
                    ]),
                    new Email([
                        'message' => 'Hiba! Kérjük érvényes e-mail címet adj meg!',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Üzenet',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Hiba! Kérjük töltsd ki az Üzenet mezőt!',
                    ]),
                ],
            ])
            ->add('button', SubmitType::class, [
                'label' => 'Küldés',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketFormDTO::class,
        ]);
    }
}
