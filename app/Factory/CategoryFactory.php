<?php

namespace App\Factory;

use App\Models\Category;
use Core\Genre\Factory\CategoryFactoryInterface as GenreCategoryFactoryInterface;
use Core\Video\Factory\CategoryFactoryInterface as VideoCategoryFactoryInterface;

class CategoryFactory implements GenreCategoryFactoryInterface, VideoCategoryFactoryInterface
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

    public function findByIdsWithGenres(array $id, array $categories)
    {
        return $this->model->whereIn('id', $categories)
            ->whereHas('genres', fn ($q) => $q->whereIn('id', $id))
            ->pluck('id')
            ->toArray();
    }
}
