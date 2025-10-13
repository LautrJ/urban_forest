<?php
// src/Command/TestCalculationsCommand.php

namespace App\Command;

use App\Repository\TreeRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-calculations',
    description: 'Test des calculs environnementaux dynamiques',
)]
class TestCalculationsCommand extends Command
{
    public function __construct(
        private TreeRepository $treeRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $trees = $this->treeRepository->findAll();

        if (empty($trees)) {
            $io->warning('Aucun arbre trouvé en base de données.');
            return Command::SUCCESS;
        }

        $io->title('Test des calculs environnementaux');

        foreach ($trees as $tree) {
            $io->section(sprintf(
                '%s (%d ans)',
                $tree->getTreeType()->getCommonName(),
                $tree->getAge()
            ));

            $io->table(
                ['Propriété', 'Valeur actuelle', 'À 10 ans', 'Maximum'],
                [
                    [
                        'Stockage CO2',
                        $tree->getCurrentCarbonStorage() . ' kg/an',
                        $tree->getProjectedCarbonStorage($tree->getAge() + 10) . ' kg/an',
                        $tree->getTreeType()->getMaxCarbonStorage() . ' kg/an'
                    ],
                    [
                        'Zone fraîcheur',
                        $tree->getCurrentCoolZoneRadius() . ' m',
                        $tree->getProjectedCoolZoneRadius($tree->getAge() + 10) . ' m',
                        $tree->getTreeType()->getMaxCoolZone() . ' m'
                    ],
                    [
                        'Maturité',
                        $tree->getMaturityPercentage() . '%',
                        '-',
                        '100%'
                    ]
                ]
            );
        }

        $io->success('Test terminé !');

        return Command::SUCCESS;
    }
}