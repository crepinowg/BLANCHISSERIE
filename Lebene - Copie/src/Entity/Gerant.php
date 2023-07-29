<?php

namespace App\Entity;

use App\Repository\GerantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GerantRepository::class)]
class Gerant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Utilisateur::class)]
    private Collection $utilisateur;

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Employe::class)]
    private Collection $employes;

    #[ORM\OneToMany(mappedBy: 'createdByGerant', targetEntity: Utilisateur::class)]
    private Collection $utilisateurCreatedBy;

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Client::class)]
    private Collection $clients;

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Depense::class)]
    private Collection $depenses;

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Notifications::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Equipe::class)]
    private Collection $equipes;

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Paiement::class)]
    private Collection $paiement;

    #[ORM\Column(length: 255)]
    private ?string $codeUi = null;

    #[ORM\OneToMany(mappedBy: 'createdByGerant', targetEntity: Rappel::class)]
    private Collection $rappels;

    #[ORM\Column]
    private ?int $salaire = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\OneToMany(mappedBy: 'gerant', targetEntity: Facture::class)]
    private Collection $facture;

    public function __construct()
    {
        $this->utilisateur = new ArrayCollection();
        $this->employes = new ArrayCollection();
        $this->utilisateurCreatedBy = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->depenses = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->equipes = new ArrayCollection();
        $this->paiement = new ArrayCollection();
        $this->rappels = new ArrayCollection();
        $this->facture = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoles(): array{
        $roles= $this->roles;

        $roles[]='ROLE_GERANT';
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
            $utilisateur->setGerant($this);
        }
        
        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getGerant() === $this) {
                $utilisateur->setGerant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function addEmploye(Employe $employe): self
    {
        if (!$this->employes->contains($employe)) {
            $this->employes[] = $employe;
            $employe->setGerant($this);
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): self
    {
        if ($this->employes->removeElement($employe)) {
            // set the owning side to null (unless already changed)
            if ($employe->getGerant() === $this) {
                $employe->setGerant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateurCreatedBy(): Collection
    {
        return $this->utilisateurCreatedBy;
    }

    public function addUtilisateurCreatedBy(Utilisateur $utilisateurCreatedBy): self
    {
        if (!$this->utilisateurCreatedBy->contains($utilisateurCreatedBy)) {
            $this->utilisateurCreatedBy[] = $utilisateurCreatedBy;
            $utilisateurCreatedBy->setCreatedByGerant($this);
        }

        return $this;
    }

    public function removeUtilisateurCreatedBy(Utilisateur $utilisateurCreatedBy): self
    {
        if ($this->utilisateurCreatedBy->removeElement($utilisateurCreatedBy)) {
            // set the owning side to null (unless already changed)
            if ($utilisateurCreatedBy->getCreatedByGerant() === $this) {
                $utilisateurCreatedBy->setCreatedByGerant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setGerant($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getGerant() === $this) {
                $client->setGerant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depense>
     */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(Depense $depense): self
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses[] = $depense;
            $depense->setGerant($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): self
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getGerant() === $this) {
                $depense->setGerant(null);
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
            $notification->setGerant($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getGerant() === $this) {
                $notification->setGerant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipe>
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes[] = $equipe;
            $equipe->setGerant($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        if ($this->equipes->removeElement($equipe)) {
            // set the owning side to null (unless already changed)
            if ($equipe->getGerant() === $this) {
                $equipe->setGerant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiement(): Collection
    {
        return $this->paiement;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiement->contains($paiement)) {
            $this->paiement[] = $paiement;
            $paiement->setGerant($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiement->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getGerant() === $this) {
                $paiement->setGerant(null);
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

    /**
     * @return Collection<int, Rappel>
     */
    public function getRappels(): Collection
    {
        return $this->rappels;
    }

    public function addRappel(Rappel $rappel): self
    {
        if (!$this->rappels->contains($rappel)) {
            $this->rappels[] = $rappel;
            $rappel->setCreatedByGerant($this);
        }

        return $this;
    }

    public function removeRappel(Rappel $rappel): self
    {
        if ($this->rappels->removeElement($rappel)) {
            // set the owning side to null (unless already changed)
            if ($rappel->getCreatedByGerant() === $this) {
                $rappel->setCreatedByGerant(null);
            }
        }

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

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFacture(): Collection
    {
        return $this->facture;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->facture->contains($facture)) {
            $this->facture[] = $facture;
            $facture->setGerant($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->facture->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getGerant() === $this) {
                $facture->setGerant(null);
            }
        }

        return $this;
    }
}
