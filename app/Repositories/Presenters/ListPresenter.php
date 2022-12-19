<?php

namespace App\Repositories\Presenters;

use Shared\Domain\Repository\ListInterface;

class ListPresenter implements ListInterface{
    /**
     * @return stdClass[]
     */
    public function items(): array
    {
        return [];
    }

    public function total(): int
    {
        return 1;
    }
}
