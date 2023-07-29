<?php

namespace App\Entity;

use App\Repository\TypeRappelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRappelRepository::class)]
class TypeRappel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
