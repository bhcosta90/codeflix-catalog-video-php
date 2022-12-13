<?php

namespace Core\Category\UseCase\DTO\Paginate;

class Input
{
    public function __construct(
        public int $page,
    ) {
        //
    }
}