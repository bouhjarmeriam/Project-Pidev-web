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

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name', // assuming 'name' is the field to be displayed
                'label' => 'Choose a Service',
                'attr' => [
                    'data-description' => $options['service_description'] ?? '', // Passing description
                    'data-price' => $options['service_price'] ?? ''  // Passing price
                ],
            ])
            ->add('date')
            ->add('patientIdentifier', TextType::class, [
                'label' => 'Patient Identifier', // Label for the patient identifier field
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
            'service_description' => '',  // Default value for description
            'service_price' => '',  // Default value for price
        ]);
    }
}
