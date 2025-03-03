<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du département est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères.",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @var Collection<int, Etage>
     */
    #[ORM\OneToMany(targetEntity: Etage::class, mappedBy: 'departement')]
    private Collection $etage;

    public function __construct()
    {
        $this->etage = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Etage>
     */
    public function getEtage(): Collection
    {
        return $this->etage;
    }

    public function addEtage(Etage $etage): static
    {
        if (!$this->etage->contains($etage)) {
            $this->etage->add($etage);
            $etage->setDepartement($this);
        }
        return $this;
    }

    public function removeEtage(Etage $etage): static
    {
        if ($this->etage->removeElement($etage)) {
            if ($etage->getDepartement() === $this) {
                $etage->setDepartement(null);
            }
        }
        return $this;
    }

    /**
     * Méthode pour calculer dynamiquement le nombre d'étages.
     */
    public function getNbreetage(): int
    {
        return $this->etage->count();
    }
}