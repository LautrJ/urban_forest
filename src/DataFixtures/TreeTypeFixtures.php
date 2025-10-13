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
                'maxCarbonStorage' => 150.0,  // kg CO2/an à maturité
                'carbonGrowthK' => 15,         // À 15 ans, atteint 50% de sa capacité (75 kg/an)
                'maxCoolZone' => 15.0,         // rayon de 15m à maturité
                'coolZoneGrowthK' => 12,       // À 12 ans, atteint 50% du rayon (7.5m)
                'maturityAge' => 30,
                'allergyPotential' => 'faible',
                'resilience' => 'élevée'
            ],
            [
                'scientificName' => 'Tilia cordata',
                'commonName' => 'Tilleul à petites feuilles',
                'maxCarbonStorage' => 120.0,
                'carbonGrowthK' => 12,
                'maxCoolZone' => 12.0,
                'coolZoneGrowthK' => 10,
                'maturityAge' => 25,
                'allergyPotential' => 'faible',
                'resilience' => 'moyenne'
            ],
            [
                'scientificName' => 'Acer platanoides',
                'commonName' => 'Érable plane',
                'maxCarbonStorage' => 100.0,
                'carbonGrowthK' => 10,
                'maxCoolZone' => 10.0,
                'coolZoneGrowthK' => 8,
                'maturityAge' => 20,
                'allergyPotential' => 'moyen',
                'resilience' => 'élevée'
            ],
            [
                'scientificName' => 'Platanus × acerifolia',
                'commonName' => 'Platane commun',
                'maxCarbonStorage' => 180.0,
                'carbonGrowthK' => 18,
                'maxCoolZone' => 18.0,
                'coolZoneGrowthK' => 15,
                'maturityAge' => 35,
                'allergyPotential' => 'moyen',
                'resilience' => 'élevée'
            ],
            [
                'scientificName' => 'Aesculus hippocastanum',
                'commonName' => 'Marronnier commun',
                'maxCarbonStorage' => 130.0,
                'carbonGrowthK' => 13,
                'maxCoolZone' => 13.0,
                'coolZoneGrowthK' => 11,
                'maturityAge' => 25,
                'allergyPotential' => 'faible',
                'resilience' => 'moyenne'
            ],
            [
                'scientificName' => 'Betula pendula',
                'commonName' => 'Bouleau verruqueux',
                'maxCarbonStorage' => 75.0,
                'carbonGrowthK' => 8,
                'maxCoolZone' => 8.0,
                'coolZoneGrowthK' => 6,
                'maturityAge' => 15,
                'allergyPotential' => 'élevé',
                'resilience' => 'faible'
            ],
            [
                'scientificName' => 'Fraxinus excelsior',
                'commonName' => 'Frêne commun',
                'maxCarbonStorage' => 140.0,
                'carbonGrowthK' => 14,
                'maxCoolZone' => 14.0,
                'coolZoneGrowthK' => 12,
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
            $treeType->setCarbonGrowthK($typeData['carbonGrowthK']);
            $treeType->setMaxCoolZone($typeData['maxCoolZone']);
            $treeType->setCoolZoneGrowthK($typeData['coolZoneGrowthK']);
            $treeType->setMaturityAge($typeData['maturityAge']);
            $treeType->setAllergyPotential($typeData['allergyPotential']);
            $treeType->setResilience($typeData['resilience']);

            $manager->persist($treeType);

            $this->addReference('tree_type_' . $index, $treeType);
        }

        $manager->flush();
    }
}