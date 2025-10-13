<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\TreeRequest;
use App\Form\TreeRequestType;
use App\Service\PhotoUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/demandes')]
#[IsGranted('ROLE_USER')]
class RequestController extends AbstractController
{
    #[Route('', name: 'app_request_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $requests = $entityManager->getRepository(TreeRequest::class)
            ->findBy(
                ['citizen' => $this->getUser()],
                ['createdAt' => 'DESC']
            );

        return $this->render('request/list.html.twig', [
            'requests' => $requests,
        ]);
    }

    #[Route('/nouvelle', name: 'app_request_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PhotoUploadService $photoUploadService
    ): Response {
        $treeRequest = new TreeRequest();
        $form = $this->createForm(TreeRequestType::class, $treeRequest);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $treeRequest->setCitizen($this->getUser());
            $treeRequest->setStatus('en_attente');
            $treeRequest->setCreatedAt(new \DateTimeImmutable());

            $appointment = new Appointment();
            $appointment->setTreeRequest($treeRequest);
            $appointment->setCitizen($this->getUser());
            $appointment->setAppointmentDate($treeRequest->getRequestedAppointmentDate());
            $appointment->setStatus('proposed');
            $appointment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($treeRequest);
            $entityManager->persist($appointment);

            $photoFiles = $form->get('photoFiles')->getData();
            if ($photoFiles) {
                $isFirst = true;
                foreach ($photoFiles as $photoFile) {
                    $photo = $photoUploadService->upload($photoFile, $this->getUser(), 'request');
                    $photo->setTreeRequest($treeRequest);

                    if ($isFirst) {
                        $photo->setIsPrimary(true);
                        $isFirst = false;
                    }

                    $entityManager->persist($photo);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de recensement a été envoyée avec succès !');

            return $this->redirectToRoute('app_request_list');
        }

        return $this->render('request/new.html.twig', [
            'form' => $form,
        ]);
    }
}