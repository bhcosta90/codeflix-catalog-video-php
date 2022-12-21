<?php

namespace Core\CastMember\UseCase\DTO\Create;

class Input
{
    public function __construct(
        public string $name,
        public int $type,
        public bool $is_active = true,
    ) {
        //
    }
}
