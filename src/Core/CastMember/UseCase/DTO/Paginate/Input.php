<?php

namespace Core\CastMember\UseCase\DTO\Paginate;

use Core\CastMember\Domain\Repository\CastMemberRepositoryFilter;

class Input
{
    public function __construct(
        public int $page,
        public ?CastMemberRepositoryFilter $filter = null,
    ) {
        //
    }
}
