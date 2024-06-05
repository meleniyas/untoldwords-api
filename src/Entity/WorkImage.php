<?php

namespace App\Entity;

use App\Repository\WorkImageRepository;
use Doctrine\ORM\Mapping as ORM;

use App\Util\Constants;
use App\Entity\Traits\DefaultTrait;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WorkImageRepository::class)]
class WorkImage
{
    use DefaultTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string', columnDefinition: 'CHAR(36) NOT NULL')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $position;

    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: "workImage")]
    #[ORM\JoinColumn(nullable: false)]

    private $image;

    #[ORM\ManyToOne(targetEntity: Work::class, inversedBy: "workImage")]
    #[ORM\JoinColumn(nullable: false)]
    private $work;

    ######################################

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->status = Constants::IMAGE_STATUS['ACTIVE'];
        $this->position = 0;
    }

    ######################################

    public function getId(): string
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
    public function getWork(): ?Work
    {
        return $this->work;
    }

    public function setWork(?Work $work): self
    {
        $this->work = $work;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}
