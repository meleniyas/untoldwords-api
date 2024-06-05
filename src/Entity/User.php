<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

use App\Util\Constants;
use App\Entity\Traits\DefaultTrait;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    use DefaultTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string', columnDefinition: 'CHAR(36) NOT NULL')]
    private string $id;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $lastName1 = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName2 = null;


    #[ORM\Column(type: 'integer')]
    private int $rol;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    ######################################

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->status = Constants::USER_STATUS['ACTIVE'];
        $this->rol = Constants::USER_ROLES['USER'];
    }

    ######################################

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getLastName1(): string
    {
        return $this->lastName1;
    }

    public function setLastName1($lastName1): static
    {
        $this->lastName1 = $lastName1;
        return $this;
    }

    public function getLastName2(): string
    {
        return $this->lastName2;
    }

    public function setLastName2($lastName2): static
    {
        $this->lastName2 = $lastName2;
        return $this;
    }


    public function getRol(): int
    {
        return $this->rol;
    }

    public function setRol(string $rol): void
    {
        if (Constants::USER_ROLES[$rol] !== null) {
            $this->rol = Constants::USER_ROLES[$rol];
        } else {
            $this->rol = Constants::USER_ROLES['USER'];
        };
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    ## Utilities ##

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
