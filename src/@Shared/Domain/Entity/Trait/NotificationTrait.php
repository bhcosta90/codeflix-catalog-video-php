<?php

namespace Shared\Domain\Entity\Trait;

use Shared\Domain\Notification\Notification;

trait NotificationTrait
{
    private ?Notification $notificationPattern = null;

    public function getNotificationTrait(): Notification
    {
        if (!$this->notificationPattern) {
            $this->notificationPattern = new Notification();
        }

        return $this->notificationPattern;
    }
}
