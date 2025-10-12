<?php

namespace App\Controller;

use App\Entity\Tree;
use App\Entity\TreeRequest;
use App\Form\ValidateTreeRequestType;
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
    ): Response {
        $form = $this->createForm(ValidateTreeRequestType::class, null, [
            'data' => [
                'treeType' => $treeRequest->getProposedTreeType(),
                'age' => $treeRequest->getEstimatedAge(),
                'latitude' => $treeRequest->getLatitude(),
                'longitude' => $treeRequest->getLongitude(),
            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();

                $tree = new Tree();
                $tree->setTreeType($data['treeType']);
                $tree->setAge($data['age']);
                $tree->setHeight($data['height']);
                $tree->setDiameter($data['diameter']);

                if ($data['plantationDate'] instanceof \DateTime) {
                    $tree->setPlantationDate(\DateTimeImmutable::createFromMutable($data['plantationDate']));
                } elseif ($data['plantationDate'] instanceof \DateTimeImmutable) {
                    $tree->setPlantationDate($data['plantationDate']);
                }

                $tree->setLatitude($data['latitude']);
                $tree->setLongitude($data['longitude']);
                $tree->setStatus($data['status']);
                $tree->setContributor($treeRequest->getCitizen());
                $tree->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($tree);

                $treeRequest->setStatus('validee');
                $treeRequest->setValidatedTree($tree);
                $treeRequest->setProcessedAt(new \DateTimeImmutable());
                $treeRequest->setProcessedBy($this->getUser());

                $entityManager->flush();

                $this->addFlash('success', 'La demande a été validée et l\'arbre a été ajouté au recensement avec succès !');

                return $this->redirectToRoute('app_admin_dashboard');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la validation : ' . $e->getMessage());
            }
        }

        return $this->render('admin/process_request.html.twig', [
            'treeRequest' => $treeRequest,
            'form' => $form,
        ]);
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