<?php

namespace App\Form;

use App\Entity\Tournoi;
use App\Entity\Jeu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le nom du tournoi
            ->add('nom', TextType::class, [
                'label' => 'Nom du Tournoi',
                'attr' => [
                    'placeholder' => 'Ex: Winter Clash 2026',
                    'class' => 'form-control bg-dark text-white border-secondary'
                ],
                'help' => 'Choisissez un nom percutant pour votre événement.',
            ])

            // Champ DATE : Doit correspondre exactement à la propriété dans l'Entité
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date et Heure de lancement',
                'widget' => 'single_text', // Important pour utiliser le sélecteur HTML5 moderne
                'attr' => [
                    'class' => 'form-control bg-dark text-white border-secondary'
                ],
            ])

            // Champ DATE FIN : optionnel, pour le calcul du statut
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date et Heure de fin',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control bg-dark text-white border-secondary'
                ],
                'help' => 'Optionnel. Permet de calculer le statut du tournoi.',
            ])

            // Champ RELATIF : Permet de choisir un jeu existant dans la base de données
            ->add('jeu', EntityType::class, [
                'class' => Jeu::class,
                'choice_label' => 'nom', // Affiche le nom du jeu dans la liste déroulante
                'label' => 'Jeu sélectionné',
                'placeholder' => 'Choisissez un jeu...',
                'attr' => [
                    'class' => 'form-select bg-dark text-white border-secondary'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}