<?php

namespace App\Controller;

use App\Entity\Tree;
use App\Entity\TreeRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/carte', name: 'app_map')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $statusPlante = $entityManager->getRepository(\App\Entity\TreeStatus::class)
            ->findOneBy(['name' => 'plante']);

        $treesEntities = $entityManager->getRepository(Tree::class)
            ->findBy(['status' => $statusPlante]);

        $trees = array_map(function($tree) {
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
                    'label' => $tree->getStatus()->getLabel(),
                ],
                'contributor' => $tree->getContributor() ? [
                    'name' => $tree->getContributor()->getName(),
                ] : null,
            ];
        }, $treesEntities);

        $requestsEntities = [];
        if ($user) {
            if ($this->isGranted('ROLE_AGENT')) {
                $requestsEntities = $entityManager->getRepository(TreeRequest::class)
                    ->findBy(['status' => 'en_attente']);
            } else {
                $requestsEntities = $entityManager->getRepository(TreeRequest::class)
                    ->findBy([
                        'citizen' => $user,
                        'status' => 'en_attente'
                    ]);
            }
        }

        $requests = array_map(function($request) {
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
        }, $requestsEntities);

        return $this->render('map/index.html.twig', [
            'trees' => $trees,
            'requests' => $requests,
        ]);
    }
}