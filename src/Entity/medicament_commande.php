<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MedicamentCommandeRepository;

#[ORM\Entity(repositoryClass: MedicamentCommandeRepository::class)]
class MedicamentCommande
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commandeMedicament")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Medicament::class, inversedBy: "commandeMedicament")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medicament $medicament = null;

    #[ORM\Column]
    private ?int $quantite = null;

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getMedicament(): ?Medicament
    {
        return $this->medicament;
    }

    public function setMedicament(?Medicament $medicament): static
    {
        $this->medicament = $medicament;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }
}
