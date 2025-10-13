<?php

namespace App\Form;

use App\Entity\TreeStatus;
use App\Entity\TreeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

            ->add('plantationDate', DateType::class, [
                'label' => 'Date de plantation (si connue)',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

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

            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => 'any',
                    'readonly' => false
                ]
            ])

            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => 'any',
                    'readonly' => false
                ]
            ])

            ->add('photoFiles', FileType::class, [
                'label' => 'Ajouter des photos supplémentaires (optionnel)',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'accept' => 'image/jpeg,image/png,image/webp',
                    'class' => 'form-control-file',
                ],
                'constraints' => [
                    new Count([
                        'max' => 5,
                        'maxMessage' => 'Maximum {{ limit }} photos',
                    ]),
                    new All([
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Format invalide (JPG, PNG ou WEBP uniquement)',
                        ])
                    ]),
                ],
                'help' => 'Vous pouvez ajouter jusqu\'à 5 photos supplémentaires',
            ])

            ->add('keepRequestPhotos', CheckboxType::class, [
                'label' => 'Conserver les photos de la demande citoyenne',
                'mapped' => false,
                'required' => false,
                'data' => true, // Coché par défaut
                'help' => 'Les photos du citoyen seront copiées vers l\'arbre validé',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas de data_class car on traite à la fois TreeRequest et Tree
        ]);
    }
}