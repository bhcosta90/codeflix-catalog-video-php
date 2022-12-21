<?php

namespace Core\CastMember\Domain\Repository;

/**
 * @codeCoverageIgnore
 */

class CastMemberRepositoryFilter
{
    public function __construct(
        public ?string $name,
        public ?int $type,
    ) {
        //
    }
}
