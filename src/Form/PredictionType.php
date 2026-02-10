<?php

namespace App\Form;

use App\Entity\Prediction;
use App\Entity\Tournoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'choice_label' => 'nom',
                'label' => 'Tournoi concerné',
                'attr' => ['class' => 'form-control']
            ])
            ->add('teamA', TextType::class, [
                'label' => 'Équipe A',
                'attr' => ['placeholder' => 'Ex: T1', 'class' => 'form-control']
            ])
            ->add('teamB', TextType::class, [
                'label' => 'Équipe B',
                'attr' => ['placeholder' => 'Ex: Gen.G', 'class' => 'form-control']
            ])
            ->add('predictedWinner', TextType::class, [
                'label' => 'Vainqueur Prédit',
                'attr' => ['placeholder' => 'Ex: T1', 'class' => 'form-control']
            ])
            ->add('winProbability', RangeType::class, [
                'label' => 'Probabilité de Victoire (IA)',
                'attr' => [
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.01,
                    'class' => 'form-range',
                    'oninput' => 'this.nextElementSibling.innerText = Math.round(this.value * 100) + "%"'
                ],
                'help' => 'Faites glisser pour ajuster la confiance.'
            ])
            ->add('scoreTeamA', IntegerType::class, [
                'label' => 'Score Équipe A (Optionnel)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('scoreTeamB', IntegerType::class, [
                'label' => 'Score Équipe B (Optionnel)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('aiAnalysis', TextareaType::class, [
                'label' => 'Analyse de l\'IA',
                'required' => false,
                'attr' => ['rows' => 4, 'class' => 'form-control', 'placeholder' => 'Pourquoi l\'IA a fait ce choix ?']
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