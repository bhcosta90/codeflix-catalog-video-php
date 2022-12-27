<?php

namespace Costa\DomainPackage\Domain\Notification\Exception;

use Exception;

class NotificationException extends Exception
{
    public array $errors;

    public function __construct(string $message, array $errors, int $code = 0)
    {
        $this->errors = $errors;
        parent::__construct($message, $code);
    }
}
