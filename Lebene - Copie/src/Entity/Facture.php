<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id ;

    #[ORM\Column]
    private ?int $totalTtc = null;

    #[ORM\Column]
    private ?int $totalTva =null;

    #[ORM\Column(nullable:true)]
    private ?float $tauxReduction;


    #[ORM\Column]
    private ?string $dateLivraison ;

    #[ORM\ManyToOne(inversedBy: 'facture')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Administrateur $admin;

   

    #[ORM\ManyToOne(inversedBy: 'factures',cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: Entete::class)]
    private Collection $entete;

    #[ORM\ManyToOne(inversedBy: 'facture')]
    private ?Livraison $livraison;

    #[Assert\Unique]
    #[ORM\Column(nullable: true)]
    private ?int $numFacture ;

    #[ORM\Column(nullable: true)]
    private ?string $etat ;

    #[ORM\Column()]
    
    private ?string $factureIdNumber = null;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: Notifications::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: FactureEquipe::class)]
    private Collection $factureEquipes;

    #[ORM\Column]
    private ?bool $express = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deliveredAt = null;

    #[ORM\Column(length: 255)]
    private ?string $dateRecuperation = null;

    #[ORM\Column(length: 255)]
    private ?string $invoiceCode = null;

    #[ORM\Column(nullable: true)]
    private ?int $joursPasser = null;

    #[ORM\Column(nullable: true)]
    private ?int $heurePasser = null;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: Rappel::class)]
    private Collection $rappels;

    #[ORM\ManyToOne(inversedBy: 'facture')]
    private ?Gerant $gerant = null;


    public function __construct()
    {
        $this->entete = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->factureEquipes = new ArrayCollection();
        $this->rappels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalTtc(): ?int
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(int $totalTtc): self
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    public function getTotalTva(): ?int
    {
        return $this->totalTva;
    }

    public function setTotalTva(int $totalTva): self
    {
        $this->totalTva = $totalTva;

        return $this;
    }

    public function getTauxReduction(): ?float
    {
        return $this->tauxReduction;
    }

    public function setTauxReduction(float $tauxReduction): self
    {
        $this->tauxReduction = $tauxReduction;

        return $this;
    }

    


    public function getDateLivraison(): ?string
    {
        return $this->dateLivraison;
    }

    public function setDateLivraison(string $dateLivraison): self
    {
        $this->dateLivraison = $dateLivraison;

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

    

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
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
            $entete->setFacture($this);
        }

        return $this;
    }

    public function removeEntete(Entete $entete): self
    {
        if ($this->entete->removeElement($entete)) {
            // set the owning side to null (unless already changed)
            if ($entete->getFacture() === $this) {
                $entete->setFacture(null);
            }
        }

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }

    

    public function getNumFacture(): ?int
    {
        return $this->numFacture;
    }

    public function setNumFacture(?int $numFacture): self
    {
        $this->numFacture = $numFacture;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getFactureIdNumber(): ?string
    {
        return $this->factureIdNumber;
    }

    public function setFactureIdNumber(string $factureIdNumber): self
    {
        $this->factureIdNumber = $factureIdNumber;

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
            $notification->setFacture($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getFacture() === $this) {
                $notification->setFacture(null);
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
            $factureEquipe->setFacture($this);
        }

        return $this;
    }

    public function removeFactureEquipe(FactureEquipe $factureEquipe): self
    {
        if ($this->factureEquipes->removeElement($factureEquipe)) {
            // set the owning side to null (unless already changed)
            if ($factureEquipe->getFacture() === $this) {
                $factureEquipe->setFacture(null);
            }
        }

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

    public function getDeliveredAt(): ?string
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(?string $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    public function getDateRecuperation(): ?string
    {
        return $this->dateRecuperation;
    }

    public function setDateRecuperation(string $dateRecuperation): self
    {
        $this->dateRecuperation = $dateRecuperation;

        return $this;
    }

    public function getInvoiceCode(): ?string
    {
        return $this->invoiceCode;
    }

    public function setInvoiceCode(string $invoiceCode): self
    {
        $this->invoiceCode = $invoiceCode;

        return $this;
    }

    public function getJoursPasser(): ?int
    {
        return $this->joursPasser;
    }

    public function setJoursPasser(?int $joursPasser): self
    {
        $this->joursPasser = $joursPasser;

        return $this;
    }

    public function getHeurePasser(): ?int
    {
        return $this->heurePasser;
    }

    public function setHeurePasser(?int $heurePasser): self
    {
        $this->heurePasser = $heurePasser;

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
            $rappel->setFacture($this);
        }

        return $this;
    }

    public function removeRappel(Rappel $rappel): self
    {
        if ($this->rappels->removeElement($rappel)) {
            // set the owning side to null (unless already changed)
            if ($rappel->getFacture() === $this) {
                $rappel->setFacture(null);
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

}
