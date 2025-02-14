<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)] 
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id] 
    #[ORM\GeneratedValue] 
    #[ORM\Column] 
    private ?int $id = null; // Identifiant unique de l'utilisateur

    #[ORM\Column(length: 180)] 
    private ?string $email = null; // L'email de l'utilisateur

    #[ORM\Column] 
    private array $roles = []; // Les rôles de l'utilisateur, comme 'ROLE_USER', 'ROLE_CANDIDATE', 'ROLE_PROFESSIONAL'

    #[ORM\Column] 
    private ?string $password = null; // Le mot de passe de l'utilisateur

    #[ORM\Column] 
    private bool $isVerified = false; // Si l'email a été vérifié ou non

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Candidate $candidate = null; // Le profil candidat (si l'utilisateur est un candidat)

    #[ORM\Column(type: 'string', length: 20)] 
    private ?string $type = '';

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Professional $professional = null;

 



    // Méthode pour obtenir l'ID de l'utilisateur
    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour obtenir l'email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Méthode pour définir l'email
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Méthode pour obtenir l'identifiant de l'utilisateur (utilisé pour la connexion)
    public function getUserIdentifier(): string
    {
        return (string) $this->email; // L'identifiant ici, c'est l'email
    }

    // Méthode pour obtenir les rôles de l'utilisateur
    public function getRoles(): array
    {
        $roles = $this->roles; // On commence par les rôles définis

        // Si c'est un professionnel, on ajoute le rôle 'ROLE_PROFESSIONAL'
        if ($this->isProfessional()) {
            $roles[] = 'ROLE_PROFESSIONAL';
        } else {
            // Sinon, on ajoute le rôle 'ROLE_CANDIDATE'
            $roles[] = 'ROLE_CANDIDATE';
        }

        // On ajoute toujours 'ROLE_USER' à tout utilisateur
        $roles[] = 'ROLE_USER';

        return array_unique($roles); // On retourne les rôles uniques
    }

    // Méthode pour définir les rôles manuellement
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Méthode pour obtenir le mot de passe
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Méthode pour définir le mot de passe
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Méthode pour effacer les informations sensibles (comme le mot de passe) après la connexion
    public function eraseCredentials(): void
    {
        // Ici, on n'a rien à effacer.
    }

    // Méthode pour savoir si l'email est vérifié
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    // Méthode pour définir si l'email est vérifié
    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    // Méthode pour obtenir le profil candidat
    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    // Méthode pour définir le profil candidat (si c'est un candidat)
    public function setCandidate(Candidate $candidate): static
    {
        if (!$this->isProfessional()) { // Si l'utilisateur est un candidat, on lie le profil
            if ($candidate->getUser() !== $this) { 
                $candidate->setUser($this); // Associe le profil candidat à l'utilisateur
            }
            $this->candidate = $candidate; // On associe le profil candidat à l'utilisateur
        } else {
            throw new \LogicException('Un professionnel ne peut pas avoir de profil candidat.');
        }

        return $this;
    }

    // Méthode pour obtenir le type d'utilisateur (candidate ou professional)
    public function getType(): ?string
    {
        return $this->type; // Retourne le type de l'utilisateur
    }

    // Méthode pour définir le type d'utilisateur ('candidate' ou 'professional')
    public function setType(string $type): static
    {
        if (!in_array($type, ['candidate', 'professional'])) {
            throw new \InvalidArgumentException('Le type doit être "candidate" ou "professional".'); // On s'assure que le type est valide
        }

        $this->type = $type;

        // Met à jour les rôles en fonction du type
        if ($type === 'professional') {
            $this->roles = ['ROLE_PROFESSIONAL']; // Si c'est un professionnel, on lui donne le rôle 'ROLE_PROFESSIONAL'
        } else {
            $this->roles = ['ROLE_CANDIDATE']; // Sinon, il est un candidat
        }

        return $this;
    }

    // Méthode pour vérifier si l'utilisateur est un professionnel
    public function isProfessional(): bool
    {
        return $this->type === 'professional'; // Si le type est 'professional', l'utilisateur est un professionnel
    }

    public function getProfessional(): ?Professional
    {
        return $this->professional;
    }

    public function setProfessional(Professional $professional): static
    {
        // set the owning side of the relation if necessary
        if ($professional->getUser() !== $this) {
            $professional->setUser($this);
        }

        $this->professional = $professional;

        return $this;
    }

  

}
