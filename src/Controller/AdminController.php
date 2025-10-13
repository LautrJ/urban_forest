<?php

namespace App\Controller;

use App\Entity\Tree;
use App\Entity\TreeRequest;
use App\Entity\User;
use App\Form\CreateAgentType;
use App\Form\CreateTreeType;
use App\Form\ValidateTreeRequestType;
use App\Service\PhotoUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        EntityManagerInterface $entityManager,
        PhotoUploadService $photoUploadService
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

                $keepRequestPhotos = $form->get('keepRequestPhotos')->getData();
                if ($keepRequestPhotos && $treeRequest->getPhotos()->count() > 0) {
                    foreach ($treeRequest->getPhotos() as $requestPhoto) {
                        $treePhoto = $photoUploadService->copyToTrees($requestPhoto);
                        $treePhoto->setTree($tree);
                        $entityManager->persist($treePhoto);
                    }
                }

                $photoFiles = $form->get('photoFiles')->getData();
                if ($photoFiles) {
                    foreach ($photoFiles as $photoFile) {
                        $photo = $photoUploadService->upload($photoFile, $this->getUser(), 'tree');
                        $photo->setTree($tree);
                        $entityManager->persist($photo);
                    }
                }

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

    #[Route('/agents', name: 'app_admin_agents_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function agentsList(EntityManagerInterface $entityManager): Response
    {
        $allUsers = $entityManager->getRepository(User::class)->findAll();

        $agents = array_filter($allUsers, function($user) {
            $roles = $user->getRoles();
            return in_array('ROLE_AGENT', $roles) || in_array('ROLE_ADMIN', $roles);
        });

        usort($agents, fn($a, $b) => strcmp($a->getName(), $b->getName()));

        return $this->render('admin/agents_list.html.twig', [
            'agents' => $agents,
        ]);
    }

    #[Route('/agents/nouveau', name: 'app_admin_agent_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function createAgent(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(CreateAgentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = new User();
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_AGENT']);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte agent a été créé avec succès !');

            return $this->redirectToRoute('app_admin_agents_list');
        }

        return $this->render('admin/agent_create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/arbre/nouveau', name: 'app_admin_tree_create')]
    public function createTree(
        Request $request,
        EntityManagerInterface $entityManager,
        PhotoUploadService $photoUploadService
    ): Response {
        $form = $this->createForm(CreateTreeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();

                $tree = new Tree();
                $tree->setTreeType($data['treeType']);
                $tree->setStatus($data['status']);
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
                $tree->setContributor($this->getUser());
                $tree->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($tree);

                $photoFiles = $form->get('photoFiles')->getData();
                if ($photoFiles) {
                    $isFirst = true;
                    foreach ($photoFiles as $photoFile) {
                        $photo = $photoUploadService->upload($photoFile, $this->getUser(), 'tree');
                        $photo->setTree($tree);

                        if ($isFirst) {
                            $photo->setIsPrimary(true);
                            $isFirst = false;
                        }

                        $entityManager->persist($photo);
                    }
                }

                $entityManager->flush();

                $this->addFlash('success', 'L\'arbre a été recensé avec succès !');

                return $this->redirectToRoute('app_admin_dashboard');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création : ' . $e->getMessage());
            }
        }

        return $this->render('admin/tree_create.html.twig', [
            'form' => $form,
        ]);
    }
}