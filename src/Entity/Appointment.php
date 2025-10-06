<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TreeRequest $treeRequest = null;

    #[ORM\ManyToOne(inversedBy: 'citizenAppointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $citizen = null;

    #[ORM\ManyToOne(inversedBy: 'agentAppointments')]
    private ?User $agent = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $proposedDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $confirmedDate = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTreeRequest(): ?TreeRequest
    {
        return $this->treeRequest;
    }

    public function setTreeRequest(?TreeRequest $treeRequest): static
    {
        $this->treeRequest = $treeRequest;

        return $this;
    }

    public function getCitizen(): ?User
    {
        return $this->citizen;
    }

    public function setCitizen(?User $citizen): static
    {
        $this->citizen = $citizen;

        return $this;
    }

    public function getAgent(): ?User
    {
        return $this->agent;
    }

    public function setAgent(?User $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getProposedDate(): ?\DateTime
    {
        return $this->proposedDate;
    }

    public function setProposedDate(?\DateTime $proposedDate): static
    {
        $this->proposedDate = $proposedDate;

        return $this;
    }

    public function getConfirmedDate(): ?\DateTime
    {
        return $this->confirmedDate;
    }

    public function setConfirmedDate(?\DateTime $confirmedDate): static
    {
        $this->confirmedDate = $confirmedDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
