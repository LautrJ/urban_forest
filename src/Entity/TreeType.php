<?php

namespace App\Entity;

use App\Repository\TreeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TreeTypeRepository::class)]
class TreeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $scientificName = null;

    #[ORM\Column(length: 255)]
    private ?string $commonName = null;

    #[ORM\Column(nullable: true)]
    private ?float $maxCarbonStorage = null;

    #[ORM\Column(nullable: true)]
    private ?int $carbonGrowthK = null;

    #[ORM\Column(nullable: true)]
    private ?float $maxCoolZone = null;

    #[ORM\Column(nullable: true)]
    private ?int $coolZoneGrowthK = null;

    #[ORM\Column(nullable: true)]
    private ?int $maturityAge = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $allergyPotential = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $resilience = null;

    /**
     * @var Collection<int, Tree>
     */
    #[ORM\OneToMany(targetEntity: Tree::class, mappedBy: 'treeType')]
    private Collection $trees;

    public function __construct()
    {
        $this->trees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function setScientificName(string $scientificName): static
    {
        $this->scientificName = $scientificName;

        return $this;
    }

    public function getCommonName(): ?string
    {
        return $this->commonName;
    }

    public function setCommonName(string $commonName): static
    {
        $this->commonName = $commonName;

        return $this;
    }

    public function getMaxCarbonStorage(): ?float
    {
        return $this->maxCarbonStorage;
    }

    public function setMaxCarbonStorage(?float $maxCarbonStorage): static
    {
        $this->maxCarbonStorage = $maxCarbonStorage;

        return $this;
    }

    public function getCarbonGrowthK(): ?float
    {
        return $this->carbonGrowthK;
    }

    public function setCarbonGrowthK(?float $carbonGrowthK): static
    {
        $this->carbonGrowthK = $carbonGrowthK;

        return $this;
    }

    public function getMaxCoolZone(): ?float
    {
        return $this->maxCoolZone;
    }

    public function setMaxCoolZone(?float $maxCoolZone): static
    {
        $this->maxCoolZone = $maxCoolZone;

        return $this;
    }

    public function getCoolZoneGrowthK(): ?float
    {
        return $this->coolZoneGrowthK;
    }

    public function setCoolZoneGrowthK(?float $coolZoneGrowthK): static
    {
        $this->coolZoneGrowthK = $coolZoneGrowthK;

        return $this;
    }

    public function getMaturityAge(): ?int
    {
        return $this->maturityAge;
    }

    public function setMaturityAge(?int $maturityAge): static
    {
        $this->maturityAge = $maturityAge;

        return $this;
    }

    public function getAllergyPotential(): ?string
    {
        return $this->allergyPotential;
    }

    public function setAllergyPotential(?string $allergyPotential): static
    {
        $this->allergyPotential = $allergyPotential;

        return $this;
    }

    public function getResilience(): ?string
    {
        return $this->resilience;
    }

    public function setResilience(?string $resilience): static
    {
        $this->resilience = $resilience;

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
            $tree->setTreeType($this);
        }

        return $this;
    }

    public function removeTree(Tree $tree): static
    {
        if ($this->trees->removeElement($tree)) {
            // set the owning side to null (unless already changed)
            if ($tree->getTreeType() === $this) {
                $tree->setTreeType(null);
            }
        }

        return $this;
    }
}
