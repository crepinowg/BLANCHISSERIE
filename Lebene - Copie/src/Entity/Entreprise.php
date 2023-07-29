<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column]
    private ?int $numeroTelEntre;

    #[ORM\Column(length: 255)]
    private ?string $emailEntre ;

    #[ORM\Column(nullable:true)]
    private ?bool $numeroEntrepriseCheck = false;

    #[ORM\Column]
    private ?bool $emailEntrepriseCheck = null;

    #[ORM\ManyToOne(inversedBy: 'entreprise')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Administrateur $administrateur = null;

    #[ORM\Column(length: 60)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $zip = null;

    public function getNumeroTelEntre(): ?int
    {
        return $this->numeroTelEntre;
    }

    public function setNumeroTelEntre(int $numeroTelEntre): self
    {
        $this->numeroTelEntre = $numeroTelEntre;

        return $this;
    }


    public function getEmailEntre(): ?string
    {
        return $this->emailEntre;
    }

    public function setEmailEntre(string $emailEntre): self
    {
        $this->emailEntre = $emailEntre;

        return $this;
    }

    public function isNumeroEntrepriseCheck(): ?bool
    {
        return $this->numeroEntrepriseCheck;
    }

    public function setNumeroEntrepriseCheck(bool $numeroEntrepriseCheck): self
    {
        $this->numeroEntrepriseCheck = $numeroEntrepriseCheck;

        return $this;
    }

    public function isEmailEntrepriseCheck(): ?bool
    {
        return $this->emailEntrepriseCheck;
    }

    public function setEmailEntrepriseCheck(bool $emailEntrepriseCheck): self
    {
        $this->emailEntrepriseCheck = $emailEntrepriseCheck;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }


}
