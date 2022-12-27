<?php

namespace App\Factory;

use App\Models\Category;
use App\Models\Genre;
use Core\Video\Factory\GenreFactoryInterface;

class GenreFactory implements GenreFactoryInterface
{
    public function __construct(private Genre $model, private Category $category)
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
