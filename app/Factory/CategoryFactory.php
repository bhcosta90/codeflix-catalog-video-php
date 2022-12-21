<?php

namespace App\Factory;

use App\Models\Category;
use Core\Genre\Factory\CategoryFactoryInterface;

class CategoryFactory implements CategoryFactoryInterface
{
    public function __construct(private Category $model)
    {
        //
    }

    public function findByIds(array $id): array
    {
        return $this->model->whereIn('id', $id)
            ->pluck('id')
            ->toArray();
    }
}
