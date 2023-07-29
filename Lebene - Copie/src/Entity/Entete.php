<?php

namespace App\Entity;

use App\Repository\EnteteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnteteRepository::class)]
class Entete
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $quantite;

    #[ORM\Column]
    private ?int $prixTotal;

    #[ORM\ManyToOne(inversedBy: 'entete',cascade:["persist","remove"])]
    #[ORM\JoinColumn(name:'facture_id', referencedColumnName:'id')]
    private ?Facture $facture ;

    #[ORM\Column]
    private ?bool $express = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateDeliveredExpress = null;

    #[ORM\Column(nullable: true)]
    private ?bool $expressDelivered = null;

    #[ORM\ManyToOne(inversedBy: 'entete')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tarifs $tarifs = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    


    public function getPrixTotal(): ?int
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(int $prixTotal): self
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }
    /*   public function getFormatTtc(): ?string
    {
         
        return number_format($this->totalTtc,0,'',' ');
    } */
    /*  public function getFormatTva(): ?string
    {
         
        return number_format($facture->getTotalTva(),0,'',' ');
    } */

     public function isExpress(): ?bool
     {
         return $this->express;
     }

     public function setExpress(bool $express): self
     {
         $this->express = $express;

         return $this;
     }

     public function getDateDeliveredExpress(): ?string
     {
         return $this->dateDeliveredExpress;
     }

     public function setDateDeliveredExpress(?string $dateDeliveredExpress): self
     {
         $this->dateDeliveredExpress = $dateDeliveredExpress;

         return $this;
     }

     public function isExpressDelivered(): ?bool
     {
         return $this->expressDelivered;
     }

     public function setExpressDelivered(bool $expressDelivered): self
     {
         $this->expressDelivered = $expressDelivered;

         return $this;
     }

     public function getTarifs(): ?Tarifs
     {
         return $this->tarifs;
     }

     public function setTarifs(?Tarifs $tarifs): self
     {
         $this->tarifs = $tarifs;

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

?>
