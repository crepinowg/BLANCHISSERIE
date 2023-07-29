<?php

namespace App\Entity;

use App\Repository\SuperAdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuperAdminRepository::class)]
class SuperAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'superAdmin', targetEntity: Utilisateur::class)]
    private Collection $utilisateur;

    #[ORM\OneToMany(mappedBy: 'superAdmin', targetEntity: CleProduit::class)]
    private Collection $cleProduit;

    public function __construct()
    {
        $this->utilisateur = new ArrayCollection();
        $this->cleProduit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateur(): Collection
    {
        return $this->utilisateur;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateur->contains($utilisateur)) {
            $this->utilisateur[] = $utilisateur;
            $utilisateur->setSuperAdmin($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getSuperAdmin() === $this) {
                $utilisateur->setSuperAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, cleProduit>
     */
    public function getCleProduit(): Collection
    {
        return $this->cleProduit;
    }

    public function addCleProduit(cleProduit $cleProduit): self
    {
        if (!$this->cleProduit->contains($cleProduit)) {
            $this->cleProduit[] = $cleProduit;
            $cleProduit->setSuperAdmin($this);
        }

        return $this;
    }

    public function removeCleProduit(cleProduit $cleProduit): self
    {
        if ($this->cleProduit->removeElement($cleProduit)) {
            // set the owning side to null (unless already changed)
            if ($cleProduit->getSuperAdmin() === $this) {
                $cleProduit->setSuperAdmin(null);
            }
        }

        return $this;
    }
}
