<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Medicament;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;


class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('medicaments', EntityType::class, [
                'class' => Medicament::class,
                'choice_label' => 'nom_medicament',
                'placeholder' => 'Sélectionnez un médicament',
                'multiple' => true,
                'required' => false,
            ])
            
            ->add('quantite', IntegerType::class, [
                'attr' => ['min' => 1],
                'label' => 'Quantité demandée',  // Added comma here
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est requise.']),
                    new Assert\Positive(['message' => 'La quantité doit être positive.'])
                ],
            ])
 
            
            ->add('dateCommande', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de livraison estimée',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de commande est requise.']),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de commande ne peut pas être dans le passé.',
                    ]),
                ],
            ])
           ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
