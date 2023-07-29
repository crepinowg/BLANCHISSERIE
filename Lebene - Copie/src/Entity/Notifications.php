<?php

namespace App\Entity;

use App\Repository\NotificationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationsRepository::class)]
class Notifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?bool $reader = null;

    #[ORM\Column(length: 255)]
    private ?string $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Administrateur $admin = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Gerant $gerant = null;


    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Client $client = null;

    #[ORM\Column(length: 60)]
    private ?string $typeNotif = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Facture $facture = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Depense $depense = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Employe $employe = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Livraison $livraison = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Equipe $equipe = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?EmployeEquipe $employe_equipe = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?Paiement $paiement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function isReader(): ?bool
    {
        return $this->reader;
    }

    public function setReader(bool $reader): self
    {
        $this->reader = $reader;

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

    public function getAdmin(): ?Administrateur
    {
        return $this->admin;
    }

    public function setAdmin(?Administrateur $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getGerant(): ?Gerant
    {
        return $this->gerant;
    }

    public function setGerant(?Gerant $gerant): self
    {
        $this->gerant = $gerant;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTypeNotif(): ?string
    {
        return $this->typeNotif;
    }

    public function setTypeNotif(string $typeNotif): self
    {
        $this->typeNotif = $typeNotif;

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

    public function getDepense(): ?Depense
    {
        return $this->depense;
    }

    public function setDepense(?Depense $depense): self
    {
        $this->depense = $depense;

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }

    public function getEmployeEquipe(): ?EmployeEquipe
    {
        return $this->employe_equipe;
    }

    public function setEmployeEquipe(?EmployeEquipe $employe_equipe): self
    {
        $this->employe_equipe = $employe_equipe;

        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): self
    {
        $this->paiement = $paiement;

        return $this;
    }
}
