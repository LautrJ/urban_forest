<?php

namespace App\Form;

use App\Entity\TreeRequest;
use App\Entity\TreeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThan;
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

            ->add('requestedAppointmentDate', DateTimeType::class, [
                'label' => 'Date et heure souhaitées pour le rendez-vous',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date de rendez-vous est obligatoire',
                    ]),
                    new GreaterThan([
                        'value' => 'now',
                        'message' => 'La date de rendez-vous doit être dans le futur',
                    ]),
                ],
                'help' => 'Proposez une date et heure qui vous conviennent. Un agent vous contactera pour confirmer.',
            ])

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
                        'maxMessage' => 'Vous ne pouvez pas uploader plus de {{ limit }} photos',
                    ]),
                    new All([
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG ou WEBP)',
                        ])
                    ]),
                ],
                'help' => 'Maximum 5 photos, 5 Mo par photo (JPG, PNG ou WEBP)',
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