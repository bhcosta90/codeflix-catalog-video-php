<?php

namespace Core\Genre\UseCase\DTO\Update;

class Input
{
    public function __construct(
        public string $id,
        public string $name,
        public ?array $categories = [],
        public bool $is_active = true,
    ) {
        //
    }
}
