<?php

// src/Form/EquipementType.php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Ajout pour le choix du statut

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ "Nom"
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'équipement', // Étiquette plus explicite
            ])
            // Champ "Type"
            ->add('type', TextType::class, [
                'label' => 'Type d\'équipement', // Ajouter une étiquette ici si nécessaire
            ])
            // Champ "Statut" avec une liste déroulante
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Fonctionnel' => 'Fonctionnel',
                    'En panne' => 'En panne',
                    'En maintenance' => 'En maintenance',
                ],
                'label' => 'Statut',
                'expanded' => false, // Utilise une liste déroulante
                'multiple' => false, // Un seul choix possible
            ])
            // Champ "Catégorie"
            ->add('category', TextType::class, [
                'label' => 'Catégorie',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class, // Lier le formulaire à l'entité Equipement
        ]);
    }
}



