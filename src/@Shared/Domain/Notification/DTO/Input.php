<?php

namespace Shared\Domain\Notification\DTO;

class Input
{
    public function __construct(
        public string $context,
        public string $message,
    ) {
    }
}
