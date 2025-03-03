<?php

namespace App\Entity;

use App\Repository\DossierMedicaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DossierMedicaleRepository::class)]
class DossierMedicale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateDeCreation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $historiqueDesMaladies = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $operationsPassees = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $consultationsPassees = null;

    #[ORM\Column(length: 255)]
    private ?string $statutDossier = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'dossierMedicale', targetEntity: Sejour::class, orphanRemoval: true)]
    private Collection $sejours;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "patient_id", referencedColumnName: "Id_User", nullable: false)]
    private ?User $patient = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "medecin_id", referencedColumnName: "Id_User", nullable: false)]
    private ?User $medecin = null;

    public function __construct()
    {
        $this->sejours = new ArrayCollection();
        $this->dateDeCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->dateDeCreation;
    }

    public function setDateDeCreation(\DateTimeInterface $dateDeCreation): static
    {
        $this->dateDeCreation = $dateDeCreation;
        return $this;
    }

    public function getHistoriqueDesMaladies(): ?string
    {
        return $this->historiqueDesMaladies;
    }

    public function setHistoriqueDesMaladies(?string $historiqueDesMaladies): static
    {
        $this->historiqueDesMaladies = $historiqueDesMaladies;
        return $this;
    }

    public function getOperationsPassees(): ?string
    {
        return $this->operationsPassees;
    }

    public function setOperationsPassees(?string $operationsPassees): static
    {
        $this->operationsPassees = $operationsPassees;
        return $this;
    }

    public function getConsultationsPassees(): ?string
    {
        return $this->consultationsPassees;
    }

    public function setConsultationsPassees(?string $consultationsPassees): static
    {
        $this->consultationsPassees = $consultationsPassees;
        return $this;
    }

    public function getStatutDossier(): ?string
    {
        return $this->statutDossier;
    }

    public function setStatutDossier(string $statutDossier): static
    {
        $this->statutDossier = $statutDossier;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Sejour>
     */
    public function getSejours(): Collection
    {
        return $this->sejours;
    }

    public function addSejour(Sejour $sejour): static
    {
        if (!$this->sejours->contains($sejour)) {
            $this->sejours->add($sejour);
            $sejour->setDossierMedicale($this);
        }

        return $this;
    }

    public function removeSejour(Sejour $sejour): static
    {
        if ($this->sejours->removeElement($sejour)) {
            // set the owning side to null (unless already changed)
            if ($sejour->getDossierMedicale() === $this) {
                $sejour->setDossierMedicale(null);
            }
        }

        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(?User $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getMedecin(): ?User
    {
        return $this->medecin;
    }

    public function setMedecin(?User $medecin): static
    {
        $this->medecin = $medecin;
        return $this;
    }
}