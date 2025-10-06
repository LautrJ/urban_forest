<?php

namespace App\Entity;

use App\Repository\TreeRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TreeRequestRepository::class)]
class TreeRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'treeRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $citizen = null;

    #[ORM\ManyToOne]
    private ?TreeType $proposedTreeType = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    private ?int $estimatedAge = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?bool $needsAppointment = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Tree $validatedTree = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

    #[ORM\ManyToOne]
    private ?User $processedBy = null;

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'treeRequest')]
    private Collection $photos;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'treeRequest')]
    private Collection $appointments;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProposedTreeType(): ?TreeType
    {
        return $this->proposedTreeType;
    }

    public function setProposedTreeType(?TreeType $proposedTreeType): static
    {
        $this->proposedTreeType = $proposedTreeType;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getEstimatedAge(): ?int
    {
        return $this->estimatedAge;
    }

    public function setEstimatedAge(?int $estimatedAge): static
    {
        $this->estimatedAge = $estimatedAge;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function isNeedsAppointment(): ?bool
    {
        return $this->needsAppointment;
    }

    public function setNeedsAppointment(bool $needsAppointment): static
    {
        $this->needsAppointment = $needsAppointment;

        return $this;
    }

    public function getValidatedTree(): ?Tree
    {
        return $this->validatedTree;
    }

    public function setValidatedTree(?Tree $validatedTree): static
    {
        $this->validatedTree = $validatedTree;

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

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    public function getProcessedBy(): ?User
    {
        return $this->processedBy;
    }

    public function setProcessedBy(?User $processedBy): static
    {
        $this->processedBy = $processedBy;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setTreeRequest($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getTreeRequest() === $this) {
                $photo->setTreeRequest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setTreeRequest($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getTreeRequest() === $this) {
                $appointment->setTreeRequest(null);
            }
        }

        return $this;
    }
}
