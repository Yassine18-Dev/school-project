<?php

namespace App\Form;

use App\Entity\Prediction;
use App\Entity\Tournoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PredictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tournoi', EntityType::class, [
                'class' => Tournoi::class,
                'choice_label' => 'nom', // Affiche le nom du tournoi dans la liste
                'label' => 'Tournoi concerné',
                'attr' => ['class' => 'form-control']
            ])
            ->add('vainqueurPredi', TextType::class, [
                'label' => 'Vainqueur suggéré par l\'IA',
                'attr' => [
                    'placeholder' => 'Ex: Team Vitality',
                    'class' => 'form-control'
                ]
            ])
            ->add('confianceAI', RangeType::class, [
                'label' => 'Indice de confiance IA (0.0 à 1.0)',
                'attr' => [
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.01,
                    'class' => 'form-range', // Classe Bootstrap pour un beau slider
                    'oninput' => 'this.nextElementSibling.value = Math.round(this.value * 100) + "%"'
                ],
                'help' => 'Faites glisser pour définir la probabilité de victoire.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prediction::class,
        ]);
    }
}