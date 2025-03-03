<?php

namespace App\Entity;

use App\Repository\EtageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EtageRepository::class)]
class Etage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numéro de l'étage est obligatoire.")]
    #[Assert\Positive(message: "Le numéro de l'étage doit être un nombre positif.")]
    private ?int $Numero = null;

    #[ORM\ManyToOne(inversedBy: 'etages')]
    #[Assert\NotNull(message: "L'étage doit être rattaché à un département.")]
    private ?Departement $departement = null;

    /**
     * @var Collection<int, Salle>
     */
    #[ORM\OneToMany(targetEntity: Salle::class, mappedBy: 'etage')]
    private Collection $salle;

    public function __construct()
    {
        $this->salle = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->Numero;
    }

    public function setNumero(int $Numero): static
    {
        $this->Numero = $Numero;
        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): static
    {
        $this->departement = $departement;
        return $this;
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getSalle(): Collection
    {
        return $this->salle;
    }

    public function addSalle(Salle $salle): static
    {
        if (!$this->salle->contains($salle)) {
            $this->salle->add($salle);
            $salle->setEtage($this);
        }
        return $this;
    }

    public function removeSalle(Salle $salle): static
    {
        if ($this->salle->removeElement($salle)) {
            if ($salle->getEtage() === $this) {
                $salle->setEtage(null);
            }
        }
        return $this;
    }

    /**
     * Méthode pour calculer dynamiquement le nombre de salles.
     */
    public function getNbrSalle(): int
    {
        return $this->salle->count();
    }
}