<?php

namespace Core\Genre\UseCase\DTO\Create;

class Input
{
    public function __construct(
        public string $name,
        public ?array $categories = null,
        public bool $is_active = true,
    ) {
        //
    }
}
