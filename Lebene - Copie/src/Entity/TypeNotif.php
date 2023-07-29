<?php

namespace App\Entity;

use App\Repository\TypeNotifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeNotifRepository::class)]
class TypeNotif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $nom = null;



    #[ORM\Column(type: Types::ARRAY)]
    private array $nomType = ["Client","Depense","Facture","Employe"];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


    public function getNomType(): array
    {
        return $this->nomType;
    }

    public function setNomType(array $nomType): self
    {
        $this->nomType = $nomType;

        return $this;
    }
}
