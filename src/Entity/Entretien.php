<?php

// src/Entity/Entretien.php
namespace App\Entity;

use App\Repository\EntretienRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EntretienRepository::class)]
class Entretien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 200)]
    /**
     * @Assert\NotBlank(message="La description est obligatoire.")
     * @Assert\Length(
     *      min=10,
     *      max=255,
     *      minMessage="La description doit comporter au moins {{ limit }} caractères.",
     *      maxMessage="La description ne peut pas dépasser {{ limit }} caractères."
     * )
     */
    private ?string $descreption = null;

    #[ORM\Column(length: 200)]
    private ?string $nom_equipement = null;

    #[ORM\ManyToOne(inversedBy: 'Entretien')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipement $equipement = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;  // Ajout de la colonne created_at

    // Constructeur pour initialiser la date de création
    public function __construct()
    {
        $this->createdAt = new \DateTime();  // Valeur par défaut de created_at
    }

    // Getter pour la propriété 'createdAt'
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Setter pour la propriété 'createdAt'
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Getters et Setters pour les autres propriétés
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getDescreption(): ?string
    {
        return $this->descreption;
    }

    public function setDescreption(string $descreption): static
    {
        $this->descreption = $descreption;
        return $this;
    }

    // Getter pour 'nom_equipement'
    public function getNomEquipement(): ?string
    {
        return $this->nom_equipement;
    }

    public function setNomEquipement(string $nom_equipement): static
    {
        $this->nom_equipement = $nom_equipement;
        return $this;
    }

    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }

    public function setEquipement(?Equipement $equipement): static
    {
        $this->equipement = $equipement;
        return $this;
    }
}



