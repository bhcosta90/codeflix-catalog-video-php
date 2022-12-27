<?php

namespace App\Repositories\Presenters;

use Costa\DomainPackage\Domain\Repository\ListInterface;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class ListPresenter implements ListInterface
{
    /**
     * @return stdClass[]
     */
    protected array $data = [];

    public function __construct(private Collection $collection)
    {
        $this->data = $this->resolveItems(
            items: $this->collection->toArray()
        );
    }

    /**
     * @return stdClass[]
     */
    public function items(): array
    {
        return (array) $this->data;
    }

    public function total(): int
    {
        return count($this->data);
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
