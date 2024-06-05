<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use App\Util\Constants;
use App\Entity\Traits\DefaultTrait;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    use DefaultTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string', columnDefinition: 'CHAR(36) NOT NULL')]
    private $id;

    #[ORM\Column(length: 180, nullable: false)]
    private $name;


    #[ORM\Column(type: 'blob')]
    private $content;


    #[ORM\Column(type: 'integer', nullable: true)]
    private $homePosition;

    #[ORM\OneToMany(targetEntity: WorkImage::class, mappedBy: "image", orphanRemoval: true, cascade: ["persist", "remove"])]
    private $workImage;

    ######################################

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->status = Constants::IMAGE_STATUS['ACTIVE'];
        $this->workImage = new ArrayCollection();
    }

    ######################################

    public function getId(): string
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|HomeImage[]
     */
    public function getHomePosition(): ?int
    {
        return $this->homePosition;
    }

    public function setHomePosition(?int $homePosition): self
    {
        $this->homePosition = $homePosition;

        return $this;
    }

    /**
     * @return Collection|WorkImage[]
     */
    public function getWorkImage(): Collection
    {
        return $this->workImage;
    }

    public function addWorkImage(WorkImage $workImage): self
    {
        if (!$this->workImage->contains($workImage)) {
            $this->workImage[] = $workImage;
            $workImage->setImage($this);
        }

        return $this;
    }

    public function removeWorkImage(WorkImage $workImage): self
    {
        if ($this->workImage->removeElement($workImage)) {
            // set the owning side to null (unless already changed)
            if ($workImage->getImage() === $this) {
                $workImage->setImage(null);
            }
        }

        return $this;
    }
}
