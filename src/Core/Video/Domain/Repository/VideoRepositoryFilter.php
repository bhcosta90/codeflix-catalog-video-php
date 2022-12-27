<?php

namespace Core\Video\Domain\Repository;

/**
 * @codeCoverageIgnore
 */
class VideoRepositoryFilter
{
    public function __construct(
        public ?string $name,
    ) {
        //
    }
}
