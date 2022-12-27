<?php

namespace Costa\DomainPackage\Domain\Repository;

use stdClass;

interface ListInterface
{
    /**
     * @return stdClass[]
     */
    public function items(): array;

    public function total(): int;
}
