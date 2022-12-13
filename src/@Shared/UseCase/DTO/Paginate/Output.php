<?php

namespace Shared\UseCase\DTO\Paginate;

class Output
{
    public function __construct(
        public array $items,
        public int $total,
        public int $per_page,
        public int $first_page,
        public int $last_page,
        public int $to,
        public int $from,
    ) {
        //
    }
}