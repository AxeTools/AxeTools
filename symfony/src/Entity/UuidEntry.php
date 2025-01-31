<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class UuidEntry {
    #[Assert\NotBlank]
    #[Assert\Uuid]
    protected string $uuid = '';

    public function getUuid(): string {
        return $this->uuid;
    }

    public function setUuid(string $uuid): UuidEntry {
        $this->uuid = $uuid;

        return $this;
    }
}
