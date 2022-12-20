<?php

namespace Core\Genre\UseCase\DTO\List;

class Output
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $is_active,
        public string $created_at,
    ) {
        //
    }
}
