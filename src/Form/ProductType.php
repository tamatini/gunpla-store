<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                "label" => "Référence"
            ])
            ->add('slug', TextType::class, [
                "label" => "Slug"
            ])
            ->add('description', TextareaType::class, [
                "label" => "Description"
            ])
            ->add('weight', TextType::class, [
                "label" => "Poids"
            ])
            ->add('width', TextType::class, [
                "label" => "Largeur"
            ])
            ->add('height', TextType::class, [
                "label" => "Hauteur"
            ])
            ->add('length', TextType::class, [
                "label" => "Longueur"
            ])
            ->add('sellPrice', NumberType::class, [
                "label" => "Prix de vente"
            ])
            ->add('stock', IntegerType::class, [
                "label" => "Quantité(s) en stock"
            ])
            ->add('scale', TextType::class, [
                "label" => "échelle"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
