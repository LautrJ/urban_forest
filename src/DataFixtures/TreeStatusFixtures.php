<?php

namespace App\DataFixtures;

use App\Entity\TreeStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TreeStatusFixtures extends Fixture
{
    public const STATUS_EN_PROJET = 'status_en_projet';
    public const STATUS_EN_DEMANDE = 'status_en_demande';
    public const STATUS_PLANTE = 'status_plante';
    public const STATUS_SUPPRIME = 'status_supprime';

    public function load(ObjectManager $manager): void
    {
        $statuses = [
            [
                'name' => 'en_projet',
                'label' => 'En projet',
                'description' => 'Arbre planifié pour une plantation future par la municipalité',
                'reference' => self::STATUS_EN_PROJET
            ],
            [
                'name' => 'en_demande',
                'label' => 'En demande',
                'description' => 'Demande de recensement soumise par un citoyen, en attente de validation',
                'reference' => self::STATUS_EN_DEMANDE
            ],
            [
                'name' => 'plante',
                'label' => 'Planté',
                'description' => 'Arbre recensé et validé, présent sur le terrain',
                'reference' => self::STATUS_PLANTE
            ],
            [
                'name' => 'supprime',
                'label' => 'Supprimé',
                'description' => 'Arbre abattu ou disparu, conservé pour historique',
                'reference' => self::STATUS_SUPPRIME
            ]
        ];

        foreach ($statuses as $statusData) {
            $status = new TreeStatus();
            $status->setName($statusData['name']);
            $status->setLabel($statusData['label']);
            $status->setDescription($statusData['description']);

            $manager->persist($status);

            $this->addReference($statusData['reference'], $status);
        }

        $manager->flush();
    }
}