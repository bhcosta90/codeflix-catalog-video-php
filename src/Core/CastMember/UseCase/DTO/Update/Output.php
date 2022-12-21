<?php

namespace Core\CastMember\UseCase\DTO\Update;

class Output
{
    public function __construct(
        public string $id,
        public string $name,
        public int $type,
        public bool $is_active,
        public string $created_at,
    ) {
        //
    }
}
