<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $nom = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 60)]
    private ?string $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Description = null;

    #[ORM\ManyToOne(inversedBy: 'equipes')]
    private ?Administrateur $administrateur = null;

    #[ORM\ManyToOne(inversedBy: 'equipes')]
    private ?Gerant $gerant = null;

    #[ORM\OneToMany(mappedBy: 'equipe', targetEntity: EmployeEquipe::class)]
    private Collection $employeEquipes;

    #[ORM\OneToMany(mappedBy: 'equipe', targetEntity: FactureEquipe::class)]
    private Collection $factureEquipes;

    #[ORM\OneToMany(mappedBy: 'equipe', targetEntity: Notifications::class)]
    private Collection $notifications;

    #[ORM\Column(length: 255)]
    private ?string $codeUi = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    public function __construct()
    {
        $this->employeEquipes = new ArrayCollection();
        $this->factureEquipes = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }
 
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

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

    public function getGerant(): ?Gerant
    {
        return $this->gerant;
    }

    public function setGerant(?Gerant $gerant): self
    {
        $this->gerant = $gerant;

        return $this;
    }

    /**
     * @return Collection<int, EmployeEquipe>
     */
    public function getEmployeEquipes(): Collection
    {
        return $this->employeEquipes;
    }

    public function addEmployeEquipe(EmployeEquipe $employeEquipe): self
    {
        if (!$this->employeEquipes->contains($employeEquipe)) {
            $this->employeEquipes[] = $employeEquipe;
            $employeEquipe->setEquipe($this);
        }

        return $this;
    }

    public function removeEmployeEquipe(EmployeEquipe $employeEquipe): self
    {
        if ($this->employeEquipes->removeElement($employeEquipe)) {
            // set the owning side to null (unless already changed)
            if ($employeEquipe->getEquipe() === $this) {
                $employeEquipe->setEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FactureEquipe>
     */
    public function getFactureEquipes(): Collection
    {
        return $this->factureEquipes;
    }

    public function addFactureEquipe(FactureEquipe $factureEquipe): self
    {
        if (!$this->factureEquipes->contains($factureEquipe)) {
            $this->factureEquipes[] = $factureEquipe;
            $factureEquipe->setEquipe($this);
        }

        return $this;
    }

    public function removeFactureEquipe(FactureEquipe $factureEquipe): self
    {
        if ($this->factureEquipes->removeElement($factureEquipe)) {
            // set the owning side to null (unless already changed)
            if ($factureEquipe->getEquipe() === $this) {
                $factureEquipe->setEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notifications>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notifications $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setEquipe($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getEquipe() === $this) {
                $notification->setEquipe(null);
            }
        }

        return $this;
    }

    public function getCodeUi(): ?string
    {
        return $this->codeUi;
    }

    public function setCodeUi(string $codeUi): self
    {
        $this->codeUi = $codeUi;

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
