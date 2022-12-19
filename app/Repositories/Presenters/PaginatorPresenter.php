<?php

namespace App\Repositories\Presenters;

use Shared\Domain\Repository\PaginationInterface;

class PaginatorPresenter implements PaginationInterface{
    /**
     * @return stdClass[]
     */
    public function items(): array
    {
        return [];
    }

    public function perPage(): int
    {
        return 1;
    }

    public function total(): int
    {
        return 1;
    }

    public function firstPage(): int
    {
        return 1;
    }

    public function lastPage(): int
    {
        return 1;
    }

    public function to(): int
    {
        return 1;
    }

    public function from(): int
    {
        return 1;
    }
}
