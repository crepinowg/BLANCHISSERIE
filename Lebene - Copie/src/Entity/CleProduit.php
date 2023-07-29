<?php

namespace App\Entity;

use App\Repository\CleProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CleProduitRepository::class)]
class CleProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cle = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column]
    private ?bool $statut = null;

    #[ORM\ManyToOne(inversedBy: 'cleProduit')]
    private ?Administrateur $administrateur = null;

    #[ORM\ManyToOne(inversedBy: 'cleProduit')]
    private ?SuperAdmin $superAdmin = null;

    #[ORM\Column]
    private ?string $createdAt = null;

    #[ORM\Column]
    private ?bool $etat = null;

    #[ORM\Column]
    private ?int $duree_reste = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $useAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCle(): ?string
    {
        return $this->cle;
    }

    public function setCle(string $cle): self
    {
        $this->cle = $cle;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getAdministrateur(): ?Administrateur
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?Administrateur $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getSuperAdmin(): ?SuperAdmin
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(?SuperAdmin $superAdmin): self
    {
        $this->superAdmin = $superAdmin;

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

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDureeReste(): ?int
    {
        return $this->duree_reste;
    }

    public function setDureeReste(int $duree_reste): self
    {
        $this->duree_reste = $duree_reste;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getUseAt(): ?string
    {
        return $this->useAt;
    }

    public function setUseAt(?string $useAt): self
    {
        $this->useAt = $useAt;

        return $this;
    }
}
