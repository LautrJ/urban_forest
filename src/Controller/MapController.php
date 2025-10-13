<?php

namespace App\Controller;

use App\Repository\TreeRepository;
use App\Repository\TreeRequestRepository;
use App\Repository\TreeStatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/carte', name: 'app_map')]
    public function index(
        TreeRepository $treeRepository,
        TreeRequestRepository $treeRequestRepository,
        TreeStatusRepository $treeStatusRepository,
        Request $request
    ): Response {
        $user = $this->getUser();
        $focusedTreeId = $request->query->get('treeId');

        if ($this->isGranted('ROLE_AGENT')) {
            $trees = $treeRepository->findAll();
            $requests = $treeRequestRepository->findBy(['status' => 'en_attente']);
        } else {
            $statusEnProjet = $treeStatusRepository->find(1);
            $statusPlante = $treeStatusRepository->find(3);
            $statusSupprime = $treeStatusRepository->find(4);

            $trees = $treeRepository->findBy([
                'status' => [$statusEnProjet, $statusPlante, $statusSupprime]
            ]);

            if ($user) {
                $requests = $treeRequestRepository->findBy([
                    'citizen' => $user,
                    'status' => 'en_attente'
                ]);
            } else {
                $requests = [];
            }
        }

        $treesData = [];
        foreach ($trees as $tree) {
            $photosData = [];
            foreach ($tree->getPhotos() as $photo) {
                $photosData[] = [
                    'webPath' => $photo->getWebPath(),
                    'originalName' => $photo->getOriginalName(),
                    'isPrimary' => $photo->isPrimary(),
                ];
            }

            $treesData[] = [
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
                    'name' => $tree->getContributor()->getName()
                ] : null,
                'environmental' => [
                    'carbonStorage' => $tree->getCurrentCarbonStorage(),
                    'coolZoneRadius' => $tree->getCurrentCoolZoneRadius(),
                    'isMature' => $tree->isMature(),
                    'maturityPercentage' => $tree->getMaturityPercentage(),
                    'allergyPotential' => $tree->getTreeType()->getAllergyPotential(),
                    'resilience' => $tree->getTreeType()->getResilience(),
                ],
                'photos' => $photosData,
            ];
        }

        $requestsData = [];
        foreach ($requests as $request) {
            $requestsData[] = [
                'id' => $request->getId(),
                'latitude' => $request->getLatitude(),
                'longitude' => $request->getLongitude(),
                'citizen' => [
                    'name' => $request->getCitizen()->getName()
                ],
                'proposedTreeType' => $request->getProposedTreeType() ? [
                    'commonName' => $request->getProposedTreeType()->getCommonName()
                ] : null,
                'estimatedAge' => $request->getEstimatedAge(),
                'status' => $request->getStatus(),
            ];
        }

        return $this->render('map/index.html.twig', [
            'trees' => json_encode($treesData),
            'requests' => json_encode($requestsData),
            'focusedTreeId' => $focusedTreeId,
            'treesCount' => count($treesData),
            'requestsCount' => count($requestsData),
        ]);
    }
}