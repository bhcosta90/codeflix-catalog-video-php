<?php

namespace Core\CastMember\Domain\Repository;

use Core\CastMember\Domain\Entity\CastMember;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;

interface CastMemberRepositoryInterface
{
    public function insert(CastMember $genre): bool;

    public function update(CastMember $genre): bool;

    public function delete(string $id): bool;

    public function findById(string $id): ?CastMember;

    public function findAll(CastMemberRepositoryFilter $filter = null): ListInterface;

    public function paginate(
        CastMemberRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface;
}
