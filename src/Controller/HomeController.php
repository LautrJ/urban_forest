<?php

namespace App\Controller;

use App\Repository\TreeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        TreeRepository $treeRepository,
        UserRepository $userRepository
    ): Response {
        $trees = $treeRepository->findAll();

        $totalTrees = count($trees);
        $totalCO2 = 0;
        $totalCoolZone = 0;
        $treeCount = 0;

        foreach ($trees as $tree) {
            $totalCO2 += $tree->getCurrentCarbonStorage();
            $coolZone = $tree->getCurrentCoolZoneRadius();
            if ($coolZone > 0) {
                $totalCoolZone += $coolZone;
                $treeCount++;
            }
        }

        $averageCoolZone = $treeCount > 0 ? $totalCoolZone / $treeCount : 0;

        $contributors = $userRepository->createQueryBuilder('u')
            ->select('COUNT(DISTINCT u.id)')
            ->where('u.trees IS NOT EMPTY')
            ->getQuery()
            ->getSingleScalarResult();

        $treesData = array_values(array_map(function($tree) {
            $photos = [];
            foreach ($tree->getPhotos() as $photo) {
                $photos[] = $photo->getWebPath();
            }

            return [
                'latitude' => $tree->getLatitude(),
                'longitude' => $tree->getLongitude(),
                'commonName' => $tree->getTreeType()->getCommonName(),
                'scientificName' => $tree->getTreeType()->getScientificName(),
                'age' => $tree->getAge(),
                'carbonStorage' => round($tree->getCurrentCarbonStorage(), 1),
                'photos' => $photos,
                'statusCode' => $tree->getStatus()->getName(),
            ];
        }, $trees));

        return $this->render('home/index.html.twig', [
            'totalTrees' => $totalTrees,
            'totalCO2' => $totalCO2,
            'averageCoolZone' => $averageCoolZone,
            'totalContributors' => $contributors,
            'treesData' => $treesData,
        ]);
    }
}