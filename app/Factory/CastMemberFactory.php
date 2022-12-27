<?php

namespace App\Factory;

use App\Models\CastMember;
use Core\Video\Factory\CastMemberFactoryInterface;

class CastMemberFactory implements CastMemberFactoryInterface
{
    public function __construct(private CastMember $model)
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
