<?php

namespace Core\Category\UseCase\DTO\Create;

class Input
{
    public function __construct(
        public string $name,
        public ?string $description,
        public bool $is_active = true,
    ) {
        //
    }
}
