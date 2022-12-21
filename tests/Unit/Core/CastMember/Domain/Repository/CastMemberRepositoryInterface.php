<?php

namespace Core\CastMember\Domain\Repository;

use Core\CastMember\Domain\Entity\CastMemberEntity;
use Shared\Domain\Repository\{ListInterface, PaginationInterface};

interface CastMemberRepositoryInterface
{
    public function insert(CastMemberEntity $genre): bool;
    public function update(CastMemberEntity $genre): bool;
    public function delete(string $id): bool;
    public function findById(string $id): ?CastMemberEntity;
    public function findAll(CastMemberRepositoryFilter $filter = null): ListInterface;
    public function paginate(
        CastMemberRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface;
}
