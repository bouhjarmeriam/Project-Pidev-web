<?php

namespace App\Entity;

use App\Repository\MedicamentRepository;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicamentRepository::class)]
class Medicament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le nom du médicament est requis.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne doit pas dépasser 255 caractères.")]
    #[ORM\Column(length: 255)]
    private ?string $nom_medicament = null;

    #[Assert\NotBlank(message: "La description est requise.")]
    #[ORM\Column(length: 255)]
    private ?string $description_medicament = null;

    #[Assert\NotBlank(message: "Le type est requis.")]
    #[ORM\Column(length: 255)]
    private ?string $type_medicament = null;

    #[Assert\NotBlank(message: "Le prix est requis.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    #[ORM\Column(type: "float")]
    private ?float $prix_medicament = null;

    #[Assert\NotBlank(message: "La quantité en stock est requise.")]
    #[Assert\PositiveOrZero(message: "La quantité ne peut pas être négative.")]
    #[ORM\Column(type: "integer")]
    private ?int $quantite_stock = null;

    #[Assert\NotBlank(message: "La date d’entrée est requise.")]
    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $date_entree = null;

    #[Assert\NotBlank(message: "La date d’expiration est requise.")]
    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $date_expiration = null;
    /**
     * @var Collection<int, Commande>
     */
    #[ORM\ManyToMany(targetEntity: Commande::class, mappedBy: 'medicaments')]
    private Collection $commande_medicament;

    public function __construct()
    {
        $this->commande_medicament = new ArrayCollection();
    }


   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMedicament(): ?string
    {
        return $this->nom_medicament;
    }

    public function setNomMedicament(string $nom_medicament): static
    {
        $this->nom_medicament = $nom_medicament;

        return $this;
    }

    public function getDescriptionMedicament(): ?string
    {
        return $this->description_medicament;
    }

    public function setDescriptionMedicament(string $description_medicament): static
    {
        $this->description_medicament = $description_medicament;

        return $this;
    }

    public function getTypeMedicament(): ?string
    {
        return $this->type_medicament;
    }

    public function setTypeMedicament(string $type_medicament): static
    {
        $this->type_medicament = $type_medicament;

        return $this;
    }

    public function getPrixMedicament(): ?float
    {
        return $this->prix_medicament;
    }

    public function setPrixMedicament(float $prix_medicament): static
    {
        $this->prix_medicament = $prix_medicament;

        return $this;
    }

    public function getQuantiteStock(): ?float
    {
        return $this->quantite_stock;
    }

    public function setQuantiteStock(float $quantite_stock): static
    {
        $this->quantite_stock = $quantite_stock;

        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->date_entree;
    }

    public function setDateEntree(\DateTimeInterface $date_entree): static
    {
        $this->date_entree = $date_entree;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(\DateTimeInterface $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    
    /**
     * @return Collection<int, Commande>
     */
    public function getCommandeMedicament(): Collection
    {
        return $this->commande_medicament;
    }

    public function addCommandeMedicament(Commande $commandeMedicament): static
    {
        if (!$this->commande_medicament->contains($commandeMedicament)) {
            $this->commande_medicament->add($commandeMedicament);
        }

        return $this;
    }

    public function removeCommandeMedicament(Commande $commandeMedicament): static
    {
        $this->commande_medicament->removeElement($commandeMedicament);

        return $this;
    }
}
