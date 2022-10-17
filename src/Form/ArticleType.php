<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'required' => true,
                'label' => "Titre *",
                'attr' => [
                    'placeholder' => 'Le tire de l\'artile'
                ] 
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required'  => true,
                'empty_data' => 'Le corps du message est vide',
                'attr'  => [
                    'placeholder' => 'Décrivez l\'article...',
                    'class' => 'textarea'
                ]
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'label' => "Photo",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
