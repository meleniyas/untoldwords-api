<?php

namespace App\Entity;

use App\Repository\WorkRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use App\Util\Constants;
use App\Entity\Traits\DefaultTrait;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WorkRepository::class)]
class Work
{
    use DefaultTrait;


    #[ORM\Id]
    #[ORM\Column(type: 'string', columnDefinition: 'CHAR(36) NOT NULL')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $position;

    #[ORM\Column(type: 'string', nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private $architects;

    #[ORM\Column(type: 'string', nullable: true)]
    private $description;


    #[ORM\OneToMany(targetEntity: WorkImage::class, mappedBy: "work", cascade: ["persist", "remove"])]
    private $workImages;

    ######################################

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->status = Constants::IMAGE_STATUS['ACTIVE'];
        $this->workImages = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getArchitects(): ?string
    {
        return $this->architects;
    }

    public function setArchitects(string $architects): self
    {
        $this->architects = $architects;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getWorkImages(): Collection
    {
        return $this->workImages;
    }

    public function addWorkImage(WorkImage $workImage): self
    {
        if (!$this->workImages->contains($workImage)) {
            $this->workImages[] = $workImage;
            $workImage->setWork($this);
        }

        return $this;
    }

    public function removeHomeImage(WorkImage $workImage): self
    {
        if ($this->workImages->removeElement($workImage)) {
            if ($workImage->getWork() === $this) {
                $workImage->setWork(null);
            }
        }

        return $this;
    }
}
