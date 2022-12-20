<?php

namespace Core\Genre\UseCase\DTO\Paginate;

use Core\Genre\Domain\Repository\GenreRepositoryFilter;

class Input
{
    public function __construct(
        public int $page,
        public ?GenreRepositoryFilter $filter = null,
    ) {
        //
    }
}
