<?php

namespace Core\Category\UseCase\DTO\Create;

class Output
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public bool $is_active,
        public string $created_at,
    ) {
        //
    }
}
