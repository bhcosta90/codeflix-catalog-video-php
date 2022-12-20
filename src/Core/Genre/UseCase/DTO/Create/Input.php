<?php

namespace Core\Genre\UseCase\DTO\Create;

class Input
{
    public function __construct(
        public string $name,
        public ?array $categories = [],
        public bool $is_active = true,
    ) {
        //
    }
}
