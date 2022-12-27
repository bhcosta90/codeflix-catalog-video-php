<?php

namespace Costa\DomainPackage\Domain\Entity\Trait;

trait EntityTrait
{
    public function id(): string
    {
        return (string) $this->id;
    }

    public function createdAt($format = 'Y-m-d H:i:s'): string
    {
        return $this->createdAt->format($format);
    }
}
