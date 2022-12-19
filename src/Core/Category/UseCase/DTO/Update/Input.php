<?php

namespace Core\Category\UseCase\DTO\Update;

class Input
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public bool $is_active,
    ) {
        //
    }
}
