<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[InheritanceType("JOINED")]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * 
     */
    #[ORM\Column(nullable:true)]
    protected ?string $photoProfile;

    #[ORM\Column(length: 30)]
    protected ?string $nom = null;

    #[ORM\Column]
    protected ?int $numero = null;

    #[ORM\Column(length: 60, nullable:true)]
    protected ?string $email = null;

    #[ORM\Column]
    protected ?bool $emailCheck = null;

    #[ORM\Column]
    protected ?bool $numeroCheck = null;

    #[ORM\Column(length: 255)]
    protected ?string $motDePasse = null;

    #[ORM\Column(length: 30)]
    protected ?string $username = null;


    #[ORM\Column(length: 30)]
    protected ?string $createdAt = null;


    #[ORM\Column(type: 'json')]
    protected ?array $roles = [];

    #[ORM\Column(length: 255)]
    protected ?string $adresse = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateur')]
    protected ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateur')]
    protected ?Gerant $gerant = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateur')]
    protected ?Administrateur $administrateur = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateur')]
    private ?Employe $employe = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurCreatedBy')]
    private ?Gerant $createdByGerant = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurCreatedBy')]
    private ?Administrateur $createdByAdmin = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etatSuspendu = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $sexe = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateur')]
    private ?SuperAdmin $superAdmin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoProfile(): ?string
    {
        return $this->photoProfile;
    }

    public function setPhotoProfile(?string $photoProfile): self
    {
        $this->photoProfile = $photoProfile;
        return $this;
    }
    

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isEmailCheck(): ?bool
    {
        return $this->emailCheck;
    }

    public function setEmailCheck(bool $emailCheck): self
    {
        $this->emailCheck = $emailCheck;

        return $this;
    }

    public function isNumeroCheck(): ?bool
    {
        return $this->numeroCheck;
    }

    public function setNumeroCheck(bool $numeroCheck): self
    {
        $this->numeroCheck = $numeroCheck;

        return $this;
    }


    public function setMotDepasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserIdentifier(): string{
        return(string) $this->username;
    }

    /**
     * @see UserInterface
     */
    
    public function getRoles(): array{
        $roles= $this->roles;
        return array_unique($roles);

    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string{

        return $this->motDePasse;

    }
   
    public function eraseCredentials(){

    }

    public function getAdresse(): ?string
    {

        return $this->adresse;

    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

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

    public function getGerant(): ?Gerant
    {
        return $this->gerant;
    }

    public function setGerant(?Gerant $gerant): self
    {
        $this->gerant = $gerant;

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

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;

        return $this;
    }

    public function getCreatedByGerant(): ?Gerant
    {
        return $this->createdByGerant;
    }

    public function setCreatedByGerant(?Gerant $createdByGerant): self
    {
        $this->createdByGerant = $createdByGerant;

        return $this;
    }

    public function getCreatedByAdmin(): ?Administrateur
    {
        return $this->createdByAdmin;
    }

    public function setCreatedByAdmin(?Administrateur $createdByAdmin): self
    {
        $this->createdByAdmin = $createdByAdmin;

        return $this;
    }

    public function isEtatSuspendu(): ?bool
    {
        return $this->etatSuspendu;
    }

    public function setEtatSuspendu(?bool $etatSuspendu): self
    {
        $this->etatSuspendu = $etatSuspendu;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getSuperAdmin(): ?SuperAdmin
    {
        return $this->superAdmin;
    }

    public function setSuperAdmin(?SuperAdmin $superAdmin): self
    {
        $this->superAdmin = $superAdmin;

        return $this;
    }

    

    
}
