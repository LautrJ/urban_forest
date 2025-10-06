<?php

namespace App\DataFixtures;

use App\Entity\TreeType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TreeTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $treeTypes = [
            [
                'scientificName' => 'Quercus robur',
                'commonName' => 'Chêne pédonculé',
                'maxCarbonStorage' => 150.0,
                'carbonGrowthCoefficient' => 0.7,
                'maxCoolZone' => 80.0,
                'coolZoneGrowthCoefficient' => 0.65,
                'maturityAge' => 30,
                'allergyPotential' => 'faible',
                'resilience' => 'élevée'
            ],
            [
                'scientificName' => 'Tilia cordata',
                'commonName' => 'Tilleul à petites feuilles',
                'maxCarbonStorage' => 120.0,
                'carbonGrowthCoefficient' => 0.65,
                'maxCoolZone' => 70.0,
                'coolZoneGrowthCoefficient' => 0.6,
                'maturityAge' => 25,
                'allergyPotential' => 'faible',
                'resilience' => 'moyenne'
            ],
            [
                'scientificName' => 'Acer platanoides',
                'commonName' => 'Érable plane',
                'maxCarbonStorage' => 110.0,
                'carbonGrowthCoefficient' => 0.68,
                'maxCoolZone' => 65.0,
                'coolZoneGrowthCoefficient' => 0.62,
                'maturityAge' => 20,
                'allergyPotential' => 'moyen',
                'resilience' => 'élevée'
            ],
            [
                'scientificName' => 'Platanus × acerifolia',
                'commonName' => 'Platane commun',
                'maxCarbonStorage' => 180.0,
                'carbonGrowthCoefficient' => 0.72,
                'maxCoolZone' => 90.0,
                'coolZoneGrowthCoefficient' => 0.7,
                'maturityAge' => 35,
                'allergyPotential' => 'moyen',
                'resilience' => 'élevée'
            ],
            [
                'scientificName' => 'Aesculus hippocastanum',
                'commonName' => 'Marronnier commun',
                'maxCarbonStorage' => 130.0,
                'carbonGrowthCoefficient' => 0.66,
                'maxCoolZone' => 75.0,
                'coolZoneGrowthCoefficient' => 0.64,
                'maturityAge' => 25,
                'allergyPotential' => 'faible',
                'resilience' => 'moyenne'
            ],
            [
                'scientificName' => 'Betula pendula',
                'commonName' => 'Bouleau verruqueux',
                'maxCarbonStorage' => 75.0,
                'carbonGrowthCoefficient' => 0.55,
                'maxCoolZone' => 50.0,
                'coolZoneGrowthCoefficient' => 0.5,
                'maturityAge' => 15,
                'allergyPotential' => 'élevé',
                'resilience' => 'faible'
            ],
            [
                'scientificName' => 'Fraxinus excelsior',
                'commonName' => 'Frêne commun',
                'maxCarbonStorage' => 140.0,
                'carbonGrowthCoefficient' => 0.69,
                'maxCoolZone' => 72.0,
                'coolZoneGrowthCoefficient' => 0.63,
                'maturityAge' => 28,
                'allergyPotential' => 'moyen',
                'resilience' => 'moyenne'
            ]
        ];

        foreach ($treeTypes as $index => $typeData) {
            $treeType = new TreeType();
            $treeType->setScientificName($typeData['scientificName']);
            $treeType->setCommonName($typeData['commonName']);
            $treeType->setMaxCarbonStorage($typeData['maxCarbonStorage']);
            $treeType->setCarbonGrowthCoefficient($typeData['carbonGrowthCoefficient']);
            $treeType->setMaxCoolZone($typeData['maxCoolZone']);
            $treeType->setCoolZoneGrowthCoefficient($typeData['coolZoneGrowthCoefficient']);
            $treeType->setMaturityAge($typeData['maturityAge']);
            $treeType->setAllergyPotential($typeData['allergyPotential']);
            $treeType->setResilience($typeData['resilience']);

            $manager->persist($treeType);

            $this->addReference('tree_type_' . $index, $treeType);
        }

        $manager->flush();
    }
}