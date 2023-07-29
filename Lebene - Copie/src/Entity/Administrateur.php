<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
class Administrateur 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id;

   

    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Tarifs::class)]
    private Collection $tarifs;

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Facture::class)]
    private Collection $facture;


    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: Utilisateur::class)]
    private Collection $utilisateur;

    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: Employe::class)]
    private Collection $employes;

    #[ORM\OneToMany(mappedBy: 'createdByAdmin', targetEntity: Utilisateur::class)]
    private Collection $utilisateurCreatedBy;

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Depense::class)]
    private Collection $depenses;

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Notifications::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Equipe::class)]
    private Collection $equipes;

    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: Paiement::class)]
    private Collection $paiement;

    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: Entreprise::class)]
    private Collection $entreprise;

    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: Client::class)]
    private Collection $client;

    #[ORM\OneToMany(mappedBy: 'createdByAdmin', targetEntity: Rappel::class)]
    private Collection $rappels;

    #[ORM\Column(nullable: true)]
    private ?bool $suspendu = null;

    #[ORM\OneToMany(mappedBy: 'administrateur', targetEntity: CleProduit::class)]
    private Collection $cleProduit;

    public function __construct()
    {
        $this->tarifs = new ArrayCollection();
        $this->facture = new ArrayCollection();
        $this->utilisateur = new ArrayCollection();
        $this->employes = new ArrayCollection();
        $this->utilisateurCreatedBy = new ArrayCollection();
        $this->depenses = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->equipes = new ArrayCollection();
        $this->paiement = new ArrayCollection();
        $this->entreprise = new ArrayCollection();
        $this->client = new ArrayCollection();
        $this->rappels = new ArrayCollection();
        $this->cleProduit = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

 
    public function getRoles(): array{
        $roles= $this->roles;

        $roles[]='ROLE_ADMIN';
        return array_unique($roles);

    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Tarifs>
     */
    public function getTarifs(): Collection
    {
        return $this->tarifs;
    }

    public function addTarif(Tarifs $tarif): self
    {
        if (!$this->tarifs->contains($tarif)) {
            $this->tarifs[] = $tarif;
            $tarif->setAdmin($this);
        }

        return $this;
    }

    public function removeTarif(Tarifs $tarif): self
    {
        if ($this->tarifs->removeElement($tarif)) {
            // set the owning side to null (unless already changed)
            if ($tarif->getAdmin() === $this) {
                $tarif->setAdmin(null);
            }
        }

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
            $facture->setAdmin($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->facture->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getAdmin() === $this) {
                $facture->setAdmin(null);
            }
        }

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
            $utilisateur->setAdministrateur($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getAdministrateur() === $this) {
                $utilisateur->setAdministrateur(null);
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
            $employe->setAdministrateur($this);
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): self
    {
        if ($this->employes->removeElement($employe)) {
            // set the owning side to null (unless already changed)
            if ($employe->getAdministrateur() === $this) {
                $employe->setAdministrateur(null);
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
            $utilisateurCreatedBy->setCreatedByAdmin($this);
        }

        return $this;
    }

    public function removeUtilisateurCreatedBy(Utilisateur $utilisateurCreatedBy): self
    {
        if ($this->utilisateurCreatedBy->removeElement($utilisateurCreatedBy)) {
            // set the owning side to null (unless already changed)
            if ($utilisateurCreatedBy->getCreatedByAdmin() === $this) {
                $utilisateurCreatedBy->setCreatedByAdmin(null);
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
            $depense->setAdmin($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): self
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getAdmin() === $this) {
                $depense->setAdmin(null);
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
            $notification->setAdmin($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getAdmin() === $this) {
                $notification->setAdmin(null);
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
            $equipe->setAdmin($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        if ($this->equipes->removeElement($equipe)) {
            // set the owning side to null (unless already changed)
            if ($equipe->getAdmin() === $this) {
                $equipe->setAdmin(null);
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
            $paiement->setAdministrateur($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiement->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getAdministrateur() === $this) {
                $paiement->setAdministrateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprise(): Collection
    {
        return $this->entreprise;
    }

    public function addEntreprise(Entreprise $entreprise): self
    {
        if (!$this->entreprise->contains($entreprise)) {
            $this->entreprise[] = $entreprise;
            $entreprise->setAdministrateur($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): self
    {
        if ($this->entreprise->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getAdministrateur() === $this) {
                $entreprise->setAdministrateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClient(): Collection
    {
        return $this->client;
    }

    public function addClient(Client $client): self
    {
        if (!$this->client->contains($client)) {
            $this->client[] = $client;
            $client->setAdministrateur($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->client->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getAdministrateur() === $this) {
                $client->setAdministrateur(null);
            }
        }

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
            $rappel->setCreatedByAdmin($this);
        }

        return $this;
    }

    public function removeRappel(Rappel $rappel): self
    {
        if ($this->rappels->removeElement($rappel)) {
            // set the owning side to null (unless already changed)
            if ($rappel->getCreatedByAdmin() === $this) {
                $rappel->setCreatedByAdmin(null);
            }
        }

        return $this;
    }

    public function isSuspendu(): ?bool
    {
        return $this->suspendu;
    }

    public function setSuspendu(?bool $suspendu): self
    {
        $this->suspendu = $suspendu;

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
            $cleProduit->setAdministrateur($this);
        }

        return $this;
    }

    public function removeCleProduit(cleProduit $cleProduit): self
    {
        if ($this->cleProduit->removeElement($cleProduit)) {
            // set the owning side to null (unless already changed)
            if ($cleProduit->getAdministrateur() === $this) {
                $cleProduit->setAdministrateur(null);
            }
        }

        return $this;
    }

    

}
