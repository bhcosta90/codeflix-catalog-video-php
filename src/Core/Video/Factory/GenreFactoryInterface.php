<?php

namespace Core\Video\Factory;

interface GenreFactoryInterface
{
    public function findByIds(array $id): array;
}
