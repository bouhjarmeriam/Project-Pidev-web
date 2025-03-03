<?php
namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la salle est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La capacité est obligatoire.")]
    #[Assert\Positive(message: "La capacité doit être un nombre positif.")]
    #[Assert\Type(type: 'integer', message: "La capacité doit être un entier.")]
    private ?int $capacite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de salle est obligatoire.")]
    #[Assert\Choice(
        choices: ['Amphithéâtre', 'Salle de classe', 'Laboratoire', 'Salle de réunion'],
        message: "Choisissez un type de salle valide : Amphithéâtre, Salle de classe, Laboratoire ou Salle de réunion."
    )]
    private ?string $type_salle = null;

    #[ORM\ManyToOne(inversedBy: 'salle')]
    #[Assert\NotNull(message: "L'étage doit être spécifié.")]
    private ?Etage $etage = null;

    #[ORM\Column(length: 255)]
    
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
#[Assert\Choice(
    choices: ['Disponible', 'Occupé', 'En maintenance'],
    message: "Choisissez un statut valide : Disponible, Occupé ou En maintenance."
)]
    private ?string $status = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    #[Assert\Range(min: 1, max: 10, notInRangeMessage: "La priorité doit être entre 1 et 10.")]
    private ?int $priorite = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'salle')]
    private Collection $reservation;

    public function __construct()
    {
        $this->reservation = new ArrayCollection();
    }

    

    // Getter et Setter pour id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour nom
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    // Getter et Setter pour capacite
    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;
        return $this;
    }

    // Getter et Setter pour type_salle
    public function getTypeSalle(): ?string
    {
        return $this->type_salle;
    }

    public function setTypeSalle(string $type_salle): self
    {
        $this->type_salle = $type_salle;
        return $this;
    }

    // Getter et Setter pour etage
    public function getEtage(): ?Etage
    {
        return $this->etage;
    }

    public function setEtage(?Etage $etage): self
    {
        $this->etage = $etage;
        return $this;
    }

    // Getter et Setter pour image
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPriorite(): ?int
    {
        return $this->priorite;
    }

    public function setPriorite(int $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
            $reservation->setSalle($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservation->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getSalle() === $this) {
                $reservation->setSalle(null);
            }
        }

        return $this;
    }


}


