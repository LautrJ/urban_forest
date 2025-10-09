<?php

namespace App\Controller;

use App\Repository\TreeRepository;
use App\Repository\TreeRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/carte', name: 'app_map')]
    public function index(
        TreeRepository $treeRepository,
        TreeRequestRepository $treeRequestRepository
    ): Response {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_AGENT')) {
            $trees = $treeRepository->findAll();
            $requests = $treeRequestRepository->findBy(['status' => 'en_attente']);
        } elseif ($user) {
            $trees = $treeRepository->findBy(['status' => ['valide', 'en_projet', 'supprime']]);
            $requests = $treeRequestRepository->findBy([
                'citizen' => $user,
                'status' => 'en_attente'
            ]);
        } else {
            $trees = $treeRepository->findBy(['status' => ['valide', 'en_projet', 'supprime']]);
            $requests = [];
        }

        $treesData = array_map(function($tree) {
            return [
                'id' => $tree->getId(),
                'latitude' => $tree->getLatitude(),
                'longitude' => $tree->getLongitude(),
                'age' => $tree->getAge(),
                'treeType' => [
                    'commonName' => $tree->getTreeType()->getCommonName(),
                    'scientificName' => $tree->getTreeType()->getScientificName(),
                ],
                'status' => [
                    'code' => $tree->getStatus()->getName(),
                    'label' => $tree->getStatus()->getLabel(),
                ],
                'contributor' => $tree->getContributor() ? [
                    'name' => $tree->getContributor()->getName(),
                ] : null,
                'environmental' => [
                    'carbonStorage' => $tree->getCurrentCarbonStorage(),
                    'coolZoneRadius' => $tree->getCurrentCoolZoneRadius(),
                    'maturityPercentage' => $tree->getMaturityPercentage(),
                    'isMature' => $tree->isMature(),
                    'allergyPotential' => $tree->getTreeType()->getAllergyPotential(),
                    'resilience' => $tree->getTreeType()->getResilience(),
                ]
            ];
        }, $trees);

        $requestsData = array_map(function($request) {
            return [
                'id' => $request->getId(),
                'latitude' => $request->getLatitude(),
                'longitude' => $request->getLongitude(),
                'estimatedAge' => $request->getEstimatedAge(),
                'proposedTreeType' => $request->getProposedTreeType() ? [
                    'commonName' => $request->getProposedTreeType()->getCommonName(),
                ] : null,
                'citizen' => [
                    'name' => $request->getCitizen()->getName(),
                ],
            ];
        }, $requests);

        return $this->render('map/index.html.twig', [
            'trees' => $treesData,
            'requests' => $requestsData,
        ]);
    }
}