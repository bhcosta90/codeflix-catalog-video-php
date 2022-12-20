<?php

namespace Core\Genre\Domain\Repository;

use Core\Genre\Domain\Entity\GenreEntity;
use Shared\Domain\Repository\{ListInterface, PaginationInterface};

interface GenreRepositoryInterface
{
    public function insert(GenreEntity $genre): bool;
    public function update(GenreEntity $genre): bool;
    public function delete(string $id): bool;
    public function findById(string $id): ?GenreEntity;
    public function findAll(GenreRepositoryFilter $filter = null): ListInterface;
    public function paginate(
        GenreRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface;
}
