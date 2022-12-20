<?php

namespace Core\Genre\Factory;

interface CategoryFactoryInterface
{
    public function findByIds(array $id): array;
}
