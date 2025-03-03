<?php
  // src/Form/ConsultationType.php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'label' => 'Service médical',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un service']),
                ],
                'attr' => [
                    'class' => 'select-custom',
                    'data-description' => $options['service_description'] ?? '',
                    'data-price' => $options['service_price'] ?? ''
                ],
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de consultation',
                'constraints' => [
                    new NotBlank(['message' => 'La date est requise']),
                    new GreaterThanOrEqual([
                        'value' => 'now',
                        'message' => 'La date doit être future'
                    ]),
                ],
            ])
            ->add('patientIdentifier', TextType::class, [
                'label' => 'Identifiant du patient',
                'constraints' => [
                    new NotBlank(['message' => 'L\'identifiant du patient est requis']),
                ],
            ])
            ->add('motif', TextareaType::class, [
                'label' => 'Motif de la consultation',
                'required' => false,
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Niveau de priorité',
                'choices' => [
                    'Normal' => 'normal',
                    'Urgent' => 'urgent',
                    'Très urgent' => 'tres_urgent'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un niveau de priorité']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
            'service_description' => '',
            'service_price' => '',
            'allow_extra_fields' => true,
        ]);
    }
}
