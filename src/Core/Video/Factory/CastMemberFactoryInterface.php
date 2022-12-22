<?php

namespace Core\Video\Factory;

interface CastMemberFactoryInterface
{
    public function findByIds(array $id): array;
}
