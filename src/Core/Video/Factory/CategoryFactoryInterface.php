<?php

namespace Core\Video\Factory;

interface CategoryFactoryInterface
{
    public function findByIds(array $id): array;
}
