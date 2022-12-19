<?php

namespace App\Repositories\Presenters;

use Shared\Domain\Repository\ListInterface;
use Illuminate\Database\Eloquent\Collection;

class ListPresenter implements ListInterface
{
    public function __construct(private Collection $data)
    {
        //
    }
    /**
     * @return stdClass[]
     */
    public function items(): array
    {
        return (array) $this->data->toArray();
    }

    public function total(): int
    {
        return count($this->items());
    }
}
