<?php

namespace Costa\DomainPackage\Domain\Notification\DTO;

class Input
{
    public function __construct(
        public string $context,
        public string $message,
    ) {
    }
}
