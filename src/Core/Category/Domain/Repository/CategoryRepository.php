<?php

namespace Core\Category\Domain\Repository;

use Core\Category\Domain\Entity\CategoryEntity;
use Core\Shared\Domain\Repository\{ListInterface, PaginationInterface};

interface CategoryRepository
{
    public function insert(CategoryEntity $category): bool;
    public function update(CategoryEntity $category): bool;
    public function delete(CategoryEntity $category): bool;
    public function toEntity(object $obj): ?CategoryEntity;
    public function findById(string $id): ?CategoryEntity;
    public function findAll(): ListInterface;
    public function paginate(int $page, int $total = 15): PaginationInterface;
}
