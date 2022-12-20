<?php

namespace Core\Genre\UseCase\DTO\Create;

class Output
{
    public function __construct(
        public string $id,
        public string $name,
        public ?array $categories,
        public bool $is_active,
        public string $created_at,
    ) {
        //
    }
}
