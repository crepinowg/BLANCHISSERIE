<?php

namespace App\Entity;

use App\Repository\TarifsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarifsRepository::class)]
class Tarifs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id;

    #[ORM\Column]
    private ?int $prix;

    #[ORM\Column(nullable:'true')]
    private ?int $nombre;

    #[ORM\Column]
    private ?int $type;

    #[ORM\ManyToOne(inversedBy: 'tarifs')]
    private ?Administrateur $admin = null;

    #[ORM\ManyToOne(inversedBy: 'tarifs')]
    private ?Icons $icons = null;

    #[ORM\Column(nullable: true)]
    private ?bool $express = null;

    #[ORM\OneToMany(mappedBy: 'tarifs', targetEntity: Entete::class)]
    private Collection $entete;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = true;

    public function __construct()
    {
        $this->entete = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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

    public function getIcons(): ?Icons
    {
        return $this->icons;
    }

    public function setIcons(?Icons $icons): self
    {
        $this->icons = $icons;

        return $this;
    }

    public function isExpress(): ?bool
    {
        return $this->express;
    }

    public function setExpress(?bool $express): self
    {
        $this->express = $express;

        return $this;
    }

    public function toArray()
    {

           return ([
                'prix' => $this->prix,
                'type' => $this->type,
                'icon' => $this->icons,
                'express' => $this->express,
                'admin' => $this->admin,
                // ajoutez les autres propriétés ici
            ]);

      
        
    }

    /**
     * @return Collection<int, Entete>
     */
    public function getEntete(): Collection
    {
        return $this->entete;
    }

    public function addEntete(Entete $entete): self
    {
        if (!$this->entete->contains($entete)) {
            $this->entete[] = $entete;
            $entete->setTarifs($this);
        }

        return $this;
    }

    public function removeEntete(Entete $entete): self
    {
        if ($this->entete->removeElement($entete)) {
            // set the owning side to null (unless already changed)
            if ($entete->getTarifs() === $this) {
                $entete->setTarifs(null);
            }
        }

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

}
