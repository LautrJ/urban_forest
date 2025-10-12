<?php

namespace App\Form;

use App\Entity\TreeStatus;
use App\Entity\TreeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CreateTreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('latitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une position sur la carte']),
                ],
            ])
            ->add('longitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une position sur la carte']),
                ],
            ])

            ->add('treeType', EntityType::class, [
                'class' => TreeType::class,
                'choice_label' => function(TreeType $treeType) {
                    return $treeType->getCommonName() . ' (' . $treeType->getScientificName() . ')';
                },
                'label' => 'Essence de l\'arbre',
                'required' => true,
                'placeholder' => 'Sélectionnez une essence',
                'attr' => ['class' => 'form-select']
            ])

            ->add('status', EntityType::class, [
                'class' => TreeStatus::class,
                'choice_label' => 'label',
                'label' => 'Statut de l\'arbre',
                'required' => true,
                'placeholder' => 'Sélectionnez un statut',
                'attr' => ['class' => 'form-select'],
                'help' => '"Planté" = arbre existant | "En projet" = plantation future'
            ])

            ->add('age', IntegerType::class, [
                'label' => 'Âge (années)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
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
                ]
            ])

            ->add('diameter', NumberType::class, [
                'label' => 'Diamètre du tronc (cm)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.1',
                    'placeholder' => 'Ex: 45.5'
                ]
            ])

            ->add('plantationDate', DateType::class, [
                'label' => 'Date de plantation (si connue)',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])

            // ⭐ PHOTOS
            ->add('photoFiles', FileType::class, [
                'label' => 'Photos de l\'arbre (optionnel)',
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
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                            'mimeTypesMessage' => 'Format invalide (JPG, PNG ou WEBP)',
                        ])
                    ]),
                ],
                'help' => 'Maximum 5 photos, 5 Mo par photo',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas de data_class car c'est un formulaire de création
        ]);
    }
}