<?php

namespace App\Entity;

use App\Repository\PharmacienRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PharmacienRepository::class)]
#[ORM\Table(name: "users")] // IMPORTANT : Même table que User !
class Pharmacien extends User
{
    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }
}
