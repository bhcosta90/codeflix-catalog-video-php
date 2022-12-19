<?php

namespace Core\Category\UseCase\DTO\Paginate;

use Core\Category\Domain\Repository\CategoryRepositoryFilter;

class Input
{
    public function __construct(
        public int $page,
        public ?CategoryRepositoryFilter $filter = null,
    ) {
        //
    }
}
