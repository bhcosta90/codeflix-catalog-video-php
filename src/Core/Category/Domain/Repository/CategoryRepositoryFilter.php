<?php

namespace Core\Category\Domain\Repository;

class CategoryRepositoryFilter
{
    public function __construct(
        public ?string $name,
    ) {
        //
    }
}
