<?php

namespace App\Form;

use App\Entity\Etage;
use App\Entity\Departement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Numero', IntegerType::class, [
                'label' => 'Numéro d\'étage',
                'attr' => [
                    'min' => 1, // Valeur minimale pour le numéro d'étage
                ],
            ])
            ->add('departement', EntityType::class, [
                'class' => Departement::class,
                'choice_label' => 'nom', // Afficher le nom du département dans la liste déroulante
                'label' => 'Département',
                'placeholder' => 'Sélectionnez un département', // Optionnel : ajoute un placeholder
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etage::class,
        ]);
    }
}