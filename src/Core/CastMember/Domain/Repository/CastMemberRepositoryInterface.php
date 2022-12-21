<?php

namespace Core\CastMember\Domain\Repository;

use Core\CastMember\Domain\Entity\CastMember;
use Shared\Domain\Repository\{ListInterface, PaginationInterface};

interface CastMemberRepositoryInterface
{
    public function insert(CastMember $category): bool;
    public function update(CastMember $category): bool;
    public function delete(string $id): bool;
    public function findById(string $id): ?CastMember;
    public function findAll(CastMemberRepositoryFilter $filter = null): ListInterface;
    public function paginate(
        CastMemberRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface;
}
