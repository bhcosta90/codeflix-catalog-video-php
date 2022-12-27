<?php

namespace Core\Category\Domain\Repository;

use Core\Category\Domain\Entity\Category;
use Costa\DomainPackage\Domain\Repository\{ListInterface, PaginationInterface};

interface CategoryRepositoryInterface
{
    public function insert(Category $category): bool;
    public function update(Category $category): bool;
    public function delete(string $id): bool;
    public function findById(string $id): ?Category;
    public function findAll(CategoryRepositoryFilter $filter = null): ListInterface;
    public function paginate(
        CategoryRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface;
}
