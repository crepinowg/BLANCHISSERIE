<?php

namespace App\Entity;

use App\Repository\FactureEquipeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureEquipeRepository::class)]
class FactureEquipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'factureEquipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $facture = null;

    #[ORM\ManyToOne(inversedBy: 'factureEquipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }
}
