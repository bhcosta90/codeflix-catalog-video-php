<?php

namespace Core\Category\UseCase\DTO\Delete;

class Input
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
    ) {
        //
    }
}