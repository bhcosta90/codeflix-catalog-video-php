<?php

namespace Core\Video\UseCase\DTO\Paginate;

use Core\Video\Domain\Repository\VideoRepositoryFilter;

class Input
{
    public function __construct(
        public int $page,
        public ?VideoRepositoryFilter $filter = null,
    ) {
        //
    }
}
