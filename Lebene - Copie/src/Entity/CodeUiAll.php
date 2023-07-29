<?php

namespace App\Entity;

use App\Repository\CodeUiAllRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodeUiAllRepository::class)]
class CodeUiAll
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codeUi = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
