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
        return $this->paginator->firstItem();
    }

    public function from(): int
    {
        return $this->paginator->lastItem();
    }

    private function resolveItems(array $items)
    {
        $response = [];
        foreach ($items as $item) {
            $std = new stdClass;
            foreach ($item as $key => $value) {
                $std->{$key} = $value;
            }
            array_push($response, $std);
        }

        return $response;
    }
}
