<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Tree>
     */
    #[ORM\OneToMany(targetEntity: Tree::class, mappedBy: 'contributor')]
    private Collection $trees;

    /**
     * @var Collection<int, TreeRequest>
     */
    #[ORM\OneToMany(targetEntity: TreeRequest::class, mappedBy: 'citizen')]
    private Collection $treeRequests;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'citizen')]
    private Collection $citizenAppointments;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'agent')]
    private Collection $agentAppointments;

    public function __construct()
    {
        $this->trees = new ArrayCollection();
        $this->treeRequests = new ArrayCollection();
        $this->citizenAppointments = new ArrayCollection();
        $this->agentAppointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Tree>
     */
    public function getTrees(): Collection
    {
        return $this->trees;
    }

    public function addTree(Tree $tree): static
    {
        if (!$this->trees->contains($tree)) {
            $this->trees->add($tree);
            $tree->setContributor($this);
        }

        return $this;
    }

    public function removeTree(Tree $tree): static
    {
        if ($this->trees->removeElement($tree)) {
            // set the owning side to null (unless already changed)
            if ($tree->getContributor() === $this) {
                $tree->setContributor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TreeRequest>
     */
    public function getTreeRequests(): Collection
    {
        return $this->treeRequests;
    }

    public function addTreeRequest(TreeRequest $treeRequest): static
    {
        if (!$this->treeRequests->contains($treeRequest)) {
            $this->treeRequests->add($treeRequest);
            $treeRequest->setCitizen($this);
        }

        return $this;
    }

    public function removeTreeRequest(TreeRequest $treeRequest): static
    {
        if ($this->treeRequests->removeElement($treeRequest)) {
            // set the owning side to null (unless already changed)
            if ($treeRequest->getCitizen() === $this) {
                $treeRequest->setCitizen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getCitizenAppointments(): Collection
    {
        return $this->citizenAppointments;
    }

    public function addCitizenAppointment(Appointment $citizenAppointment): static
    {
        if (!$this->citizenAppointments->contains($citizenAppointment)) {
            $this->citizenAppointments->add($citizenAppointment);
            $citizenAppointment->setCitizen($this);
        }

        return $this;
    }

    public function removeCitizenAppointment(Appointment $citizenAppointment): static
    {
        if ($this->citizenAppointments->removeElement($citizenAppointment)) {
            // set the owning side to null (unless already changed)
            if ($citizenAppointment->getCitizen() === $this) {
                $citizenAppointment->setCitizen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAgentAppointments(): Collection
    {
        return $this->agentAppointments;
    }

    public function addAgentAppointment(Appointment $agentAppointment): static
    {
        if (!$this->agentAppointments->contains($agentAppointment)) {
            $this->agentAppointments->add($agentAppointment);
            $agentAppointment->setAgent($this);
        }

        return $this;
    }

    public function removeAgentAppointment(Appointment $agentAppointment): static
    {
        if ($this->agentAppointments->removeElement($agentAppointment)) {
            // set the owning side to null (unless already changed)
            if ($agentAppointment->getAgent() === $this) {
                $agentAppointment->setAgent(null);
            }
        }

        return $this;
    }
}