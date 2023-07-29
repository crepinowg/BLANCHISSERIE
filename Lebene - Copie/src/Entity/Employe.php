<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?int $salaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneExperience = null;

    #[ORM\Column( nullable: true)]
    private ?bool $typePaiement = null;

    #[ORM\Column(type: 'json')]
    protected ?array $roles = [];

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Utilisateur::class)]
    private Collection $utilisateur;

    #[ORM\ManyToOne(inversedBy: 'employes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Administrateur $administrateur = null;


    #[ORM\ManyToOne(inversedBy: 'employes')]
    private ?Gerant $gerant = null;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Notifications::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: EmployeEquipe::class)]
    private Collection $employeEquipes;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Paiement::class)]
    private Collection $paiements;

    #[ORM\Column(length: 255)]
    private ?string $codeUi = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    

    public function __construct()
    {
        $this->utilisateur = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->employeEquipes = new ArrayCollection();
        $this->paiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSalaire(): ?int
    {
        return $this->salaire;
    }

    public function setSalaire(int $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getAnneExperience(): ?int
    {
        return $this->anneExperience;
    }

    public function setAnneExperience(?int $anneExperience): self
    {
        $this->anneExperience = $anneExperience;

        return $this;
    }

    public function isTypePaiement(): ?bool
    {
        return $this->typePaiement;
    }

    public function setTypePaiement(bool $typePaiement): self
    {
        $this->typePaiement = $typePaiement;

        return $this;
    }

    public function getRoles(): array{
        $roles= $this->roles;

        $roles[]='ROLE_EMPLOYE';
        return array_unique($roles);

    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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
            $utilisateur->setEmploye($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getEmploye() === $this) {
                $utilisateur->setEmploye(null);
            }
        }

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
            $notification->setEmploye($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getEmploye() === $this) {
                $notification->setEmploye(null);
            }
        }

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
            $employeEquipe->setEmploye($this);
        }

        return $this;
    }

    public function removeEmployeEquipe(EmployeEquipe $employeEquipe): self
    {
        if ($this->employeEquipes->removeElement($employeEquipe)) {
            // set the owning side to null (unless already changed)
            if ($employeEquipe->getEmploye() === $this) {
                $employeEquipe->setEmploye(null);
            }
        }

        return $this;
    }

   /* public function __toString()
    {
        return $this->getEmploye();
    }
*/

   /**
    * @return Collection<int, Paiement>
    */
   public function getPaiements(): Collection
   {
       return $this->paiements;
   }

   public function addPaiement(Paiement $paiement): self
   {
       if (!$this->paiements->contains($paiement)) {
           $this->paiements[] = $paiement;
           $paiement->setEmploye($this);
       }

       return $this;
   }

   public function removePaiement(Paiement $paiement): self
   {
       if ($this->paiements->removeElement($paiement)) {
           // set the owning side to null (unless already changed)
           if ($paiement->getEmploye() === $this) {
               $paiement->setEmploye(null);
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
