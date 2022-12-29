<?php

namespace Core\Genre\Domain\Repository;

use Core\Genre\Domain\Entity\Genre;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;

interface GenreRepositoryInterface
{
    public function insert(Genre $genre): bool;

    public function update(Genre $genre): bool;

    public function delete(string $id): bool;

    public function findById(string $id): ?Genre;

    public function findAll(GenreRepositoryFilter $filter = null): ListInterface;

    public function paginate(
        GenreRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface;
}
