<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id ;

    #[ORM\Column(length: 255)]
    private ?string $dateRecup ;

    #[ORM\Column(length: 255)]
    private ?string $dateLivr;

    #[ORM\Column(length: 255)]
    private ?string $statut ;

    #[ORM\OneToMany(mappedBy: 'livraison', targetEntity: Facture::class,cascade: ['remove'])]
    private Collection $facture;

    #[ORM\ManyToOne(inversedBy: 'livraisons',cascade: ['remove'])]
    private ?Client $client = null;

    #[ORM\Column]
    private ?bool $express = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $delivredAt = null;

    #[ORM\OneToMany(mappedBy: 'livraison', targetEntity: Notifications::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->facture = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRecup(): ?string
    {
        return $this->dateRecup;
    }

    public function setDateRecup(string $dateRecup): self
    {
        $this->dateRecup = $dateRecup;

        return $this;
    }

    public function getDateLivr(): ?string
    {
        return $this->dateLivr;
    }

    public function setDateLivr(string $dateLivr): self
    {
        $this->dateLivr = $dateLivr;

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
           $facture->setLivraison($this);
       }

       return $this;
   }

   public function removeFacture(Facture $facture): self
   {
       if ($this->facture->removeElement($facture)) {
           // set the owning side to null (unless already changed)
           if ($facture->getLivraison() === $this) {
               $facture->setLivraison(null);
           }
       }

       return $this;
   }

   public function getClient(): ?Client
   {
       return $this->client;
   }

   public function setClient(?Client $client): self
   {
       $this->client = $client;

       return $this;
   }

   public function isExpress(): ?bool
   {
       return $this->express;
   }

   public function setExpress(bool $express): self
   {
       $this->express = $express;

       return $this;
   }

   public function getDelivredAt(): ?string
   {
       return $this->delivredAt;
   }

   public function setDelivredAt(?string $delivredAt): self
   {
       $this->delivredAt = $delivredAt;

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
           $notification->setLivraison($this);
       }

       return $this;
   }

   public function removeNotification(Notifications $notification): self
   {
       if ($this->notifications->removeElement($notification)) {
           // set the owning side to null (unless already changed)
           if ($notification->getLivraison() === $this) {
               $notification->setLivraison(null);
           }
       }

       return $this;
   }
}
