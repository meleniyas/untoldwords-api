<?php

namespace App\Entity;

use App\Repository\HomeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use App\Util\Constants;
use App\Entity\Traits\DefaultTrait;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HomeRepository::class)]
class Home
{
    use DefaultTrait;


    #[ORM\Id]
    #[ORM\Column(type: 'string', columnDefinition: 'CHAR(36) NOT NULL')]
    private $id;


    #[ORM\Column(type: 'integer', nullable: false)]
    private $timer;


    ######################################

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->status = Constants::IMAGE_STATUS['ACTIVE'];
    }

    ######################################

    public function getId(): string
    {
        return $this->id;
    }

    public function getTimer(): ?int
    {
        return $this->timer;
    }

    public function setTimer(int $timer): self
    {
        $this->timer = $timer;

        return $this;
    }
}
