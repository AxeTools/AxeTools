<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

final class TextEntry {
    #[Assert\NotBlank]
    protected string $data = '';

    protected string $dump_type = '';

    /**
     * @return string
     */
    public function getData() {
        return $this->data;
    }

    public function setData(string $data): self {
        $this->data = $data;

        return $this;
    }

    public function getDumpType(): string {
        return $this->dump_type;
    }

    public function setDumpType(string $dump_type): self {
        $this->dump_type = $dump_type;

        return $this;
    }
}
