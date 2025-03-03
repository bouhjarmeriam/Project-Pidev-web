<?php
namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\PrePersistEventArgs; // <-- Utilisez PrePersistEventArgs
use Doctrine\ORM\Event\PreRemoveEventArgs;  // <-- Utilisez PreRemoveEventArgs
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: "La date de début doit être ultérieure ou égale à aujourd'hui."
    )]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThan(
        propertyPath: 'dateDebut',
        message: "La date de fin doit être postérieure à la date de début."
    )]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Salle $salle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    #[ORM\PrePersist]
    public function setSalleStatus(PrePersistEventArgs $args): void // <-- Utilisez PrePersistEventArgs
    {
        $salle = $this->getSalle();
        if ($salle) {
            $salle->setStatus('Occupé');
        }
    }

    #[ORM\PreRemove]
    public function releaseSalle(PreRemoveEventArgs $args): void // <-- Utilisez PreRemoveEventArgs
    {
        $salle = $this->getSalle();
        if ($salle) {
            $salle->setStatus('Disponible');
        }
    }

    public function __toString(): string
    {
        return sprintf(
            'Réservation #%d - Salle: %s (%s à %s)',
            $this->id,
            $this->salle ? $this->salle->getNom() : 'Aucune salle',
            $this->dateDebut ? $this->dateDebut->format('Y-m-d H:i') : 'N/A',
            $this->dateFin ? $this->dateFin->format('Y-m-d H:i') : 'N/A'
        );
    }
}