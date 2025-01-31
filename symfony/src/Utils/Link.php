<?php

namespace App\Utils;

class Link {
    protected string $link = '';
    protected string $description = '';
    protected string $target = '_blank';

    public function __construct(string $link, string $description, string $target = '_blank') {
        $link_check = filter_var($link, FILTER_VALIDATE_URL);
        if (false === $link_check) {
            throw new \InvalidArgumentException('The link provided is not valid: '.$link);
        }
        $this->link = $link;
        $this->description = $description;
        $this->target = $target;
    }

    public function getLink(): string {
        return $this->link;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getTarget(): string {
        return $this->target;
    }
}
