<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $nom = null;

    #[ORM\Column(length: 200)]
    private ?string $type = null;

    #[ORM\Column(length: 200)]
    private ?string $statut = null;

    #[ORM\Column(length: 200)]
    private ?string $category = null;

    /**
     * @var Collection<int, Entretien>
     */
    #[ORM\OneToMany(targetEntity: Entretien::class, mappedBy: 'equipement', orphanRemoval: true)]
    private Collection $Entretien;

    public function __construct()
    {
        $this->Entretien = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Entretien>
     */
    public function getEntretien(): Collection
    {
        return $this->Entretien;
    }

    public function addEntretien(Entretien $entretien): static
    {
        if (!$this->Entretien->contains($entretien)) {
            $this->Entretien->add($entretien);
            $entretien->setEquipement($this);
        }

        return $this;
    }

    public function removeEntretien(Entretien $entretien): static
    {
        if ($this->Entretien->removeElement($entretien)) {
            // set the owning side to null (unless already changed)
            if ($entretien->getEquipement() === $this) {
                $entretien->setEquipement(null);
            }
        }

        return $this;
    }
}
