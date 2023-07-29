<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id;
    
    #[ORM\Column(length: 255)]
    private ?string $zip ;

    #[ORM\Column(length: 255)]
    private ?string $indication ;

    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

    #[Assert\Unique]
    
    #[ORM\Column(length: 255)]
    private ?string $statut ;

    

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Facture::class)]
    private Collection $factures;

   

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Livraison::class)]
    private Collection $livraisons;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Utilisateur::class)]
    private Collection $createdBy;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Utilisateur::class)]
    private Collection $utilisateur;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Gerant $gerant = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Notifications::class)]
    private Collection $notifications;

    #[ORM\Column(length: 255)]
    private ?string $codeUi = null;

    #[ORM\ManyToOne(inversedBy: 'client')]
    private ?Administrateur $administrateur = null;

    #[ORM\Column(nullable: true)]
    private ?bool $deleted = null;

    

    public function __construct()
    {
        $this->factures = new ArrayCollection();
        $this->livraisons = new ArrayCollection();
        $this->createdBy = new ArrayCollection();
        $this->utilisateur = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIndication(): ?string
    {
        return $this->indication;
    }

    public function setIndication(string $indication): self
    {
        $this->indication = $indication;

        return $this;
    }

    public function getRoles(): array{
        $roles= $this->roles;

        $roles[]='ROLE_CLIENT';
        return array_unique($roles);

    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->setClient($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getClient() === $this) {
                $facture->setClient(null);
            }
        }

        return $this;
    }


    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Livraison>
     */
    public function getLivraisons(): Collection
    {
        return $this->livraisons;
    }

    public function addLivraison(Livraison $livraison): self
    {
        if (!$this->livraisons->contains($livraison)) {
            $this->livraisons[] = $livraison;
            $livraison->setClient($this);
        }

        return $this;
    }

    public function removeLivraison(Livraison $livraison): self
    {
        if ($this->livraisons->removeElement($livraison)) {
            // set the owning side to null (unless already changed)
            if ($livraison->getClient() === $this) {
                $livraison->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getCreatedBy(): Collection
    {
        return $this->createdBy;
    }

    public function addCreatedBy(Utilisateur $createdBy): self
    {
        if (!$this->createdBy->contains($createdBy)) {
            $this->createdBy[] = $createdBy;
            $createdBy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedBy(Utilisateur $createdBy): self
    {
        if ($this->createdBy->removeElement($createdBy)) {
            // set the owning side to null (unless already changed)
            if ($createdBy->getCreatedBy() === $this) {
                $createdBy->setCreatedBy(null);
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
            $utilisateur->setClient($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getClient() === $this) {
                $utilisateur->setClient(null);
            }
        }

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
            $notification->setClient($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getClient() === $this) {
                $notification->setClient(null);
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

    public function getAdministrateur(): ?Administrateur
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?Administrateur $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }


}
