<?php

namespace Core\Category\Domain\Repository;

/**
 * @codeCoverageIgnore
 */
class CategoryRepositoryFilter
{
    public function __construct(
        public ?string $name,
    ) {
        //
    }
}
