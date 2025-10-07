<?php

namespace App\Form;

use App\Entity\TreeRequest;
use App\Entity\TreeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class TreeRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('latitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Les coordonnées GPS sont requises']),
                ],
            ])
            ->add('longitude', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Les coordonnées GPS sont requises']),
                ],
            ])

            ->add('proposedTreeType', EntityType::class, [
                'class' => TreeType::class,
                'choice_label' => 'commonName',
                'label' => 'Essence de l\'arbre (si vous la connaissez)',
                'placeholder' => 'Je ne connais pas l\'essence',
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('estimatedAge', IntegerType::class, [
                'label' => 'Âge estimé de l\'arbre (en années)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 15',
                    'min' => 1,
                    'max' => 500,
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 500,
                        'notInRangeMessage' => 'L\'âge doit être entre {{ min }} et {{ max }} ans',
                    ]),
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description / Commentaires',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Ajoutez des informations complémentaires sur l\'arbre (état, emplacement précis, etc.)',
                ],
            ])

            ->add('needsAppointment', CheckboxType::class, [
                'label' => 'Je souhaite un rendez-vous avec un agent pour valider les informations sur place',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TreeRequest::class,
        ]);
    }
}