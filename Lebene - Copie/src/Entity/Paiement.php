<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $paiementAt = null;

    #[ORM\Column(length: 60)]
    private ?string $moyenPaiement = null;

    #[ORM\Column(nullable: true)]
    private ?int $augmentation = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    private ?Employe $employe = null;

    #[ORM\ManyToOne(inversedBy: 'paiement')]
    private ?Administrateur $administrateur = null;

    #[ORM\ManyToOne(inversedBy: 'paiement')]
    private ?Gerant $gerant = null;

    #[ORM\Column]
    private ?int $paiement_final = null;

    #[ORM\OneToMany(mappedBy: 'paiement', targetEntity: Notifications::class)]
    private Collection $notifications;

    #[ORM\Column]
    private ?int $salaireInitital = null;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaiementAt(): ?string
    {
        return $this->paiementAt;
    }

    public function setPaiementAt(string $paiementAt): self
    {
        $this->paiementAt = $paiementAt;

        return $this;
    }

    public function getMoyenPaiement(): ?string
    {
        return $this->moyenPaiement;
    }

    public function setMoyenPaiement(string $moyenPaiement): self
    {
        $this->moyenPaiement = $moyenPaiement;

        return $this;
    }

    public function getAugmentation(): ?int
    {
        return $this->augmentation;
    }

    public function setAugmentation(?int $augmentation): self
    {
        $this->augmentation = $augmentation;

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

    public function getPaiementFinal(): ?int
    {
        return $this->paiement_final;
    }

    public function setPaiementFinal(int $paiement_final): self
    {
        $this->paiement_final = $paiement_final;

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
            $notification->setPaiement($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getPaiement() === $this) {
                $notification->setPaiement(null);
            }
        }

        return $this;
    }

    public function getSalaireInitital(): ?int
    {
        return $this->salaireInitital;
    }

    public function setSalaireInitital(int $salaireInitital): self
    {
        $this->salaireInitital = $salaireInitital;

        return $this;
    }
}
