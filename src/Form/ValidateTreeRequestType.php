<?php

namespace App\Form;

use App\Entity\TreeStatus;
use App\Entity\TreeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Positive;

class ValidateTreeRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Essence de l'arbre (peut être modifiée par l'agent)
            ->add('treeType', EntityType::class, [
                'class' => TreeType::class,
                'choice_label' => function(TreeType $treeType) {
                    return $treeType->getCommonName() . ' (' . $treeType->getScientificName() . ')';
                },
                'label' => 'Essence de l\'arbre',
                'required' => true,
                'placeholder' => 'Sélectionnez une essence',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])

            // Âge (peut être modifié par l'agent)
            ->add('age', IntegerType::class, [
                'label' => 'Âge (années)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Ex: 25'
                ],
                'constraints' => [
                    new Positive(message: 'L\'âge doit être positif')
                ]
            ])

            // Hauteur (nouveau champ)
            ->add('height', NumberType::class, [
                'label' => 'Hauteur (mètres)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.1',
                    'placeholder' => 'Ex: 12.5'
                ],
                'constraints' => [
                    new Positive(message: 'La hauteur doit être positive')
                ]
            ])

            // Diamètre (nouveau champ)
            ->add('diameter', NumberType::class, [
                'label' => 'Diamètre du tronc (cm)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.1',
                    'placeholder' => 'Ex: 45.5'
                ],
                'constraints' => [
                    new Positive(message: 'Le diamètre doit être positif')
                ]
            ])

            // Date de plantation (optionnelle)
            ->add('plantationDate', DateType::class, [
                'label' => 'Date de plantation (si connue)',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            // Statut de l'arbre
            ->add('status', EntityType::class, [
                'class' => TreeStatus::class,
                'choice_label' => 'label',
                'label' => 'Statut de l\'arbre',
                'required' => true,
                'placeholder' => 'Sélectionnez un statut',
                'attr' => [
                    'class' => 'form-select'
                ],
                'help' => '"Validé" = arbre existant | "En projet" = plantation future'
            ])

            // Latitude (peut être modifiée)
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => 'any',
                    'readonly' => false
                ]
            ])

            // Longitude (peut être modifiée)
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => 'any',
                    'readonly' => false
                ]
            ])

            // Photos à ajouter - DÉSACTIVÉ TEMPORAIREMENT
            /*
            ->add('photos', FileType::class, [
                'label' => 'Ajouter des photos',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg,image/png,image/webp',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Count(
                        max: 5,
                        maxMessage: 'Vous ne pouvez pas télécharger plus de {{ limit }} photos'
                    ),
                    new All([
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, WEBP)',
                        ])
                    ])
                ],
                'help' => 'Maximum 5 photos, 5 Mo par photo. Formats: JPEG, PNG, WEBP'
            ])
            */

            // Action sur les photos existantes de la demande - DÉSACTIVÉ TEMPORAIREMENT
            /*
            ->add('keepRequestPhotos', ChoiceType::class, [
                'label' => 'Photos de la demande du citoyen',
                'choices' => [
                    'Conserver toutes les photos' => 'keep_all',
                    'Supprimer toutes les photos' => 'delete_all',
                ],
                'data' => 'keep_all',
                'expanded' => true,
                'mapped' => false,
                'required' => true
            ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas de data_class car on traite à la fois TreeRequest et Tree
        ]);
    }
}