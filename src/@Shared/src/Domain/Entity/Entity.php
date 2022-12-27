<?php

namespace Costa\DomainPackage\Domain\Entity;

use Shared\Domain\Notification\Notification;

abstract class Entity
{
    private ?Notification $notification = null;

    use Trait\EntityTrait, Trait\MethodsMagicsTrait;

    public function getNotification()
    {
        if (!$this->notification) {
            $this->notification = new Notification();
        }

        return $this->notification;
    }
}
