<?php

namespace Core\Shared\Domain\Entity\Trait;

trait EntityTrait
{
    public function id(): string
    {
        return $this->id;
    }

    public function createdAt($format = 'Y-m-d H:i:s'): string
    {
        return $this->createdAt->format($format);
    }
}
