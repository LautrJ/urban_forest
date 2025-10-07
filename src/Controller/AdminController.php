<?php

namespace App\Controller;

use App\Entity\Tree;
use App\Entity\TreeStatus;
use App\Entity\TreeRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_AGENT')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $pendingRequests = $entityManager->getRepository(TreeRequest::class)
            ->findBy(
                ['status' => 'en_attente'],
                ['createdAt' => 'DESC']
            );

        $trees = $entityManager->getRepository(Tree::class)
            ->findBy([], ['createdAt' => 'DESC']);

        $totalRequests = $entityManager->getRepository(TreeRequest::class)->count([]);
        $validatedRequests = $entityManager->getRepository(TreeRequest::class)->count(['status' => 'validee']);
        $rejectedRequests = $entityManager->getRepository(TreeRequest::class)->count(['status' => 'refusee']);

        return $this->render('admin/dashboard.html.twig', [
            'pendingRequests' => $pendingRequests,
            'trees' => $trees,
            'stats' => [
                'total' => $totalRequests,
                'pending' => count($pendingRequests),
                'validated' => $validatedRequests,
                'rejected' => $rejectedRequests,
            ]
        ]);
    }

    #[Route('/demande/{id}/traiter', name: 'app_admin_request_process')]
    public function processRequest(
        TreeRequest $treeRequest,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        return $this->render('admin/process_request.html.twig', [
            'treeRequest' => $treeRequest,
        ]);
    }

    #[Route('/demande/{id}/valider', name: 'app_admin_request_validate', methods: ['POST'])]
    public function validateRequest(
        TreeRequest $treeRequest,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Créer l'arbre à partir de la demande
        $tree = new Tree();
        $tree->setLatitude($treeRequest->getLatitude());
        $tree->setLongitude($treeRequest->getLongitude());
        $tree->setTreeType($treeRequest->getProposedTreeType());
        $tree->setAge($treeRequest->getEstimatedAge());
        $tree->setContributor($treeRequest->getCitizen());
        $tree->setCreatedAt(new \DateTimeImmutable());

        // Récupérer le statut "planté" depuis la base
        $statusPlante = $entityManager->getRepository(TreeStatus::class)
            ->findOneBy(['name' => 'plante']);
        $tree->setStatus($statusPlante);

        // Mettre à jour la demande
        $treeRequest->setStatus('validee');
        $treeRequest->setValidatedTree($tree);
        $treeRequest->setProcessedAt(new \DateTimeImmutable());
        $treeRequest->setProcessedBy($this->getUser());

        $entityManager->persist($tree);
        $entityManager->flush();

        $this->addFlash('success', 'La demande a été validée et l\'arbre a été ajouté au recensement.');

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/demande/{id}/refuser', name: 'app_admin_request_reject', methods: ['POST'])]
    public function rejectRequest(
        TreeRequest $treeRequest,
        EntityManagerInterface $entityManager
    ): Response
    {
        $treeRequest->setStatus('refusee');
        $treeRequest->setProcessedAt(new \DateTimeImmutable());
        $treeRequest->setProcessedBy($this->getUser());

        $entityManager->flush();

        $this->addFlash('success', 'La demande a été refusée.');

        return $this->redirectToRoute('app_admin_dashboard');
    }
}