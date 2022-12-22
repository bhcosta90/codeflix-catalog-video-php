<?php

namespace Core\Video\Factory;

interface GenreFactoryInterface
{
    public function findByIds(array $id): array;

    public function findByIdsWithCategories(array $id, array $categories);
}
