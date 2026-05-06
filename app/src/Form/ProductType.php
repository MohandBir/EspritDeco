<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Composer\Semver\Constraint\Constraint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank(
                        ['message' => 'Ce champ est obligatoire'],
                    ),
                    new Length([
                        'max' => 255,
                        'min' => 3,
                        'maxMessage' => 'Ce champ ne doit pas depasser {{ limit }} caractères',
                        'minMessage' => 'Ce champ doit avoir au minimum {{ limit }} caractères',
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire'
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Ce champ doit avoir au minimum {{ limit }} caractères',
                    ])
                ]
            ])
            ->add('price',MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'divisor' => 1,
                'constraints' => [
                    new Positive([
                        'message' => 'Le Prix doit être strictement positif',
                    ])
                ]
            ])
            ->add('images',FileType::class, [
                'label' => '',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'constraints' => [
                    new All([
                        new Image([
                            'maxSize' => '2M',
                            'mimeTypes' => ['image/jpg','image/jpeg','image/png','image/webp'],
                            'maxSizeMessage' => 'l\'image ne doit pas dépasse 2M',
                            'mimeTypesMessage' => 'les format spportées : jpg, jpeg, png, webp',
                            'uploadErrorMessage' => 'Erreur lors de chargement de l\'image',
                        ])
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une catégorie',
                    ])
                ]
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
