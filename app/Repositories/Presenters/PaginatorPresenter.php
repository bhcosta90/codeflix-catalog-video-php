<?php

namespace App\Repositories\Presenters;

use Illuminate\Pagination\LengthAwarePaginator;
use Shared\Domain\Repository\PaginationInterface;
use stdClass;

class PaginatorPresenter implements PaginationInterface
{
    /**
     * @return stdClass[]
     */
    protected array $data = [];

    public function __construct(private LengthAwarePaginator $paginator)
    {
        $this->data = $this->resolveItems(
            items: $this->paginator->items()
        );
    }

    /**
     * @return stdClass[]
     */
    public function items(): array
    {
        return $this->data;
    }

    public function perPage(): int
    {
        return $this->paginator->perPage();
    }

    public function total(): int
    {
        return $this->paginator->total();
    }

    public function currentPage(): int
    {
        return $this->paginator->currentPage();
    }

    public function firstPage(): int
    {
        return 1;
    }

    public function lastPage(): int
    {
        return $this->paginator->lastPage();
    }

    public function to(): int
    {
        return $this->paginator->firstItem() ?? 0;
    }

    public function from(): int
    {
        return $this->paginator->lastItem() ?? 0;
    }

    private function resolveItems(array $items)
    {
        $response = [];
        foreach ($items as $item) {
            $std = new stdClass;
            foreach ($item->toArray() as $key => $value) {
                $std->{$key} = $value;
            }
            array_push($response, $std);
        }

        return $response;
    }
}
