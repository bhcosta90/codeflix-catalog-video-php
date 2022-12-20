<?php

namespace Core\Genre\Domain\Repository;

class GenreRepositoryFilter
{
    public function __construct(
        public ?string $name,
        public ?array $categories,
    ) {
        //
    }
}
