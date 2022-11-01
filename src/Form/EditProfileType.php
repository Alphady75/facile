<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom*",
                'attr' => ['placeholder' => 'Nom', 'class' => 'border rounded'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => "Prénom*",
                'attr' => ['placeholder' => 'Prénom', 'class' => 'border rounded'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => "Sexe*",
                'attr' => ['placeholder' => 'Sexe', 'class' => 'border rounded'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                ],
                'choices'  => [
                    'Masculin' => 'Masculin',
                    'Feminin' => 'Feminin',
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'required' => false,
                'help' => 'Jour/mois/année',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'Date de naissance', 'class' => 'border rounded'],
            ])
            ->add('adresse', TextType::class, [
                'label' => "Adresse*",
                'attr' => ['placeholder' => 'Adresse', 'class' => 'border rounded'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                ],
            ])
            ->add('codepostal', TextType::class, [
                'label' => "Code postal *",
                'attr' => ['placeholder' => 'Code postal', 'class' => 'border rounded'],
                'required' => false,
            ])
            ->add('ville', TextType::class, [
                'label' => "Ville *",
                'attr' => ['placeholder' => 'Ville', 'class' => 'border rounded'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => "Téléphone *",
                'attr' => ['placeholder' => 'Téléphone', 'class' => 'border rounded'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                ],
            ])
            ->add('entreprise', TextType::class, [
                'label' => "Entreprise",
                'attr' => ['placeholder' => 'Entreprise', 'class' => 'border rounded'],
                'required' => false,
            ])
            ->add('activite', TextType::class, [
                'label' => "Activité",
                'required' => false,
                'attr' => ['placeholder' => 'Activité', 'class' => 'border rounded'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adress E-mail*',
                'help' => 'Exemple: email@domaine.com',
                'attr' => ['placeholder' => 'Exemple@domail.com', 'class' => 'border rounded'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas être vide',
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir une adresse email valide',
                    ]),
                ],
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
