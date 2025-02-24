<?php

namespace App\Form;

use App\Entity\Medicament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MedicamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomMedicament', TextType::class, [
                'label' => 'Nom du Médicament',
                'attr' => ['class' => 'form-control']
            ])
            ->add('descriptionMedicament', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control']
            ])
            ->add('typeMedicament', TextType::class, [
                'label' => 'Type de Médicament',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prixMedicament', NumberType::class, [
                'label' => 'Prix (€)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('quantiteStock', IntegerType::class, [
                'label' => 'Quantité en Stock',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateEntree', DateType::class, [
                'label' => 'Date d’Entrée',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateExpiration', DateType::class, [
                'label' => 'Date d’Expiration',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medicament::class,
        ]);
    }
}
