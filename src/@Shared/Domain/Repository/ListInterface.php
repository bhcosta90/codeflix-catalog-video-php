<?php

namespace Shared\Domain\Repository;

use stdClass;

interface ListInterface
{
    /**
     * @return stdClass[]
     */
    public function items(): array;

    public function total(): int;
}
