<?php

namespace App\Entity\Traits;

use App\Util\Constants;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait DefaultTrait
{

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column]
    private bool $is_removed = false;

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        if ($this->status === 1) {
            $this->is_removed = false;
        }

        return $this;
    }

    public function getIsRemoved(): ?bool
    {
        return $this->is_removed;
    }

    public function setIsremoved(bool $is_removed): self
    {
        $this->is_removed = $is_removed;

        if ($this->is_removed) {
            $this->status = Constants::USER_STATUS['NOT_ACTIVE'];
        }

        return $this;
    }
}
