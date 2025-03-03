<?php
namespace App\Form;

use App\Entity\Etage;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la salle',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de la salle est obligatoire.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La capacité est obligatoire.']),
                    new Assert\Positive(['message' => 'La capacité doit être un nombre positif.']),
                    new Assert\Type(['type' => 'integer', 'message' => 'La capacité doit être un entier.']),
                ],
            ])
            ->add('type_salle', ChoiceType::class, [
                'label' => 'Type de salle',
                'choices' => [
                    'Amphithéâtre' => 'Amphithéâtre',
                    'Salle de classe' => 'Salle de classe',
                    'Laboratoire' => 'Laboratoire',
                    'Salle de réunion' => 'Salle de réunion',
                ],
                'expanded' => false, // Mettre true pour des boutons radio
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('etage', EntityType::class, [
                'class' => Etage::class,
                'label' => 'Étage',
                'choice_label' => 'id', // Vous pouvez changer en 'numero' si l'étage a un numéro
                'placeholder' => 'Sélectionner un étage',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du salle',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],  // Cela signifie que ce champ ne sera pas mappé directement à une propriété dans l'entité
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => 'Disponible',
                    'Occupé' => 'Occupé',
                    'En maintenance' => 'En maintenance',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('priorite', IntegerType::class, [
                'label' => 'Priorité',
                'attr' => ['class' => 'form-control'],
                
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
