<?php

namespace App\Entity;

use App\Repository\IconsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IconsRepository::class)]
class Icons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $syntaxeIcon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomIcon = null;

    #[ORM\OneToMany(mappedBy: 'icons', targetEntity: Tarifs::class)]
    private Collection $tarifs;

    public function __construct()
    {
        $this->tarifs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSyntaxeIcon(): ?string
    {
        return $this->syntaxeIcon;
    }

    public function setSyntaxeIcon(?string $syntaxeIcon): self
    {
        $this->syntaxeIcon = $syntaxeIcon;

        return $this;
    }

    public function getNomIcon(): ?string
    {
        return $this->nomIcon;
    }

    public function setNomIcon(?string $nomIcon): self
    {
        $this->nomIcon = $nomIcon;

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
            $tarif->setIcons($this);
        }

        return $this;
    }

    public function removeTarif(Tarifs $tarif): self
    {
        if ($this->tarifs->removeElement($tarif)) {
            // set the owning side to null (unless already changed)
            if ($tarif->getIcons() === $this) {
                $tarif->setIcons(null);
            }
        }

        return $this;
    }
}
