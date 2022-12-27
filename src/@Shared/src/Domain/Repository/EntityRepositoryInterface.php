<?php

namespace Costa\DomainPackage\Domain\Repository;

use Costa\DomainPackage\Domain\Entity\Entity;
use Costa\DomainPackage\Domain\Repository\{ListInterface, PaginationInterface};

interface EntityRepositoryInterface
{
    public function insert(Entity $category): bool;
    public function update(Entity $category): bool;
    public function delete(string $id): bool;
    public function filter($input = null): void;
    public function findById(string $id): ?Entity;
    public function findAll(): ListInterface;
    public function paginate(
        int $page = 1,
        int $total = 15
    ): PaginationInterface;
}
