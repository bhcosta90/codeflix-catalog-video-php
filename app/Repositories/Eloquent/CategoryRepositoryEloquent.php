<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Presenters\{ListPresenter, PaginatorPresenter};
use Core\Category\Domain\Entity\CategoryEntity;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Shared\Domain\Repository\ListInterface;
use Shared\Domain\Repository\PaginationInterface;

class CategoryRepositoryEloquent implements CategoryRepositoryInterface
{
    public function __construct(private Category $model)
    {
        //
    }

    public function insert(CategoryEntity $category): bool
    {
        $this->model->create([
            'id' => $category->id(),
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive,
            'created_at' => $category->createdAt(),
        ]);

        return true;
    }

    public function update(CategoryEntity $category): bool
    {
        return true;
    }

    public function delete(CategoryEntity $category): bool
    {
        return true;
    }

    public function findAll(): ListInterface
    {
        return new ListPresenter($this->model->get());
    }

    public function findById(string $id): ?CategoryEntity
    {
        if($obj = $this->model->find($id)){
            return new CategoryEntity(
                name: $obj->name,
                description: $obj->description,
                isActive: $obj->is_active,
                id: $obj->id,
                createdAt: $obj->created_at,
            );
        }

        throw new DomainNotFoundException("Category {$id} not found");
    }

    public function paginate(int $page, int $total = 15): PaginationInterface
    {
        return new PaginatorPresenter();
    }
}
