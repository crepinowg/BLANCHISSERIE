<?php

namespace App\Entity;

use App\Repository\RappelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RappelRepository::class)]
class Rappel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jourAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heureAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $allDay = null;

    #[ORM\Column(length: 255)]
    private ?string $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'rappels')]
    private ?Administrateur $createdByAdmin = null;

    #[ORM\ManyToOne(inversedBy: 'rappels')]
    private ?Gerant $createdByGerant = null;

    #[ORM\Column]
    private ?int $typeRappel = null;

    #[ORM\ManyToOne(inversedBy: 'rappels')]
    private ?Facture $facture = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $dateFinAt = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $heureFinAt = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getJourAt(): ?string
    {
        return $this->jourAt;
    }

    public function setJourAt(string $jourAt): self
    {
        $this->jourAt = $jourAt;

        return $this;
    }

    public function getHeureAt(): ?string
    {
        return $this->heureAt;
    }

    public function setHeureAt(string $heureAt): self
    {
        $this->heureAt = $heureAt;

        return $this;
    }

    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(?bool $allDay): self
    {
        $this->allDay = $allDay;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedByAdmin(): ?Administrateur
    {
        return $this->createdByAdmin;
    }

    public function setCreatedByAdmin(?Administrateur $createdByAdmin): self
    {
        $this->createdByAdmin = $createdByAdmin;

        return $this;
    }

    public function getCreatedByGerant(): ?Gerant
    {
        return $this->createdByGerant;
    }

    public function setCreatedByGerant(?Gerant $createdByGerant): self
    {
        $this->createdByGerant = $createdByGerant;

        return $this;
    }

    public function getTypeRappel(): ?int
    {
        return $this->typeRappel;
    }

    public function setTypeRappel(int $typeRappel): self
    {
        $this->typeRappel = $typeRappel;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    public function getDateFinAt(): ?string
    {
        return $this->dateFinAt;
    }

    public function setDateFinAt(string $dateFinAt): self
    {
        $this->dateFinAt = $dateFinAt;

        return $this;
    }

    public function getHeureFinAt(): ?string
    {
        return $this->heureFinAt;
    }

    public function setHeureFinAt(string $heureFinAt): self
    {
        $this->heureFinAt = $heureFinAt;

        return $this;
    }
}
