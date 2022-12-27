<?php

namespace Costa\DomainPackage\Domain\Repository;

use stdClass;

interface PaginationInterface
{
    /**
     * @return stdClass[]
     */
    public function items(): array;

    public function perPage(): int;

    public function total(): int;

    public function currentPage(): int;

    public function firstPage(): int;

    public function lastPage(): int;

    public function to(): int;

    public function from(): int;
}
