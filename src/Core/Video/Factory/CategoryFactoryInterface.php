<?php

namespace Core\Video\Factory;

interface CategoryFactoryInterface
{
    public function findByIds(array $id): array;

    public function findByIdsWithGenres(array $id, array $genres);
}
