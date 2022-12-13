<?php

namespace Core\Category\UseCase\DTO\List;

class Output
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public bool $active,
    ) {
        //
    }
}
