<?php

// src/Entity/Entretien.php
namespace App\Entity;

use App\Repository\EntretienRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

    // Getter pour la propriété 'nom_equipement'
    public function getNomEquipement(): ?string
    {
        return $this->nom_equipement;
    }

    // Autres getters et setters...

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

