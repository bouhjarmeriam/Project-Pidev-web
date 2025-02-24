<?php

 // src/Form/FormNameType.php
namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // For text input
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // For textarea input
use Symfony\Component\Form\Extension\Core\Type\NumberType; // For numeric input
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)  // Simple text input
            ->add('description', TextareaType::class)  // This is for the large text area
            ->add('price', NumberType::class);  // Numeric input for price
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
