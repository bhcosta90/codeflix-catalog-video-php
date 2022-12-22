<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use App\Repositories\Presenters\{ListPresenter, PaginatorPresenter};
use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\CategoryRepositoryFilter;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Shared\Domain\Repository\ListInterface;
use Shared\Domain\Repository\PaginationInterface;

class CategoryRepositoryEloquent implements CategoryRepositoryInterface
{
    public function __construct(private CategoryModel $model)
    {
        //
    }

    public function insert(Category $entity): bool
    {
        $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'description' => $entity->description,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);

        return true;
    }

    public function update(Category $entity): bool
    {
        if ($obj = $this->model->find($entity->id())) {
            return (bool) $obj->update([
                'name' => $entity->name,
                'description' => $entity->description,
                'is_active' => $entity->isActive,
            ]);
        }

        throw new DomainNotFoundException("Category {$entity->id()} not found");
    }

    public function delete(string $id): bool
    {
        if ($obj = $this->model->find($id)) {
            return $obj->delete();
        }

        throw new DomainNotFoundException("Category {$id} not found");
    }

    public function findAll(CategoryRepositoryFilter $filter = null): ListInterface
    {
        return new ListPresenter($this->filter($filter)->get());
    }

    public function findById(string $id): ?Category
    {
        if ($obj = $this->model->find($id)) {
            $response = new Category(
                name: $obj->name,
                description: $obj->description,
                id: $obj->id,
                createdAt: $obj->created_at,
            );

            ((bool) $obj->is_active) ? $response->enabled() : $response->disabled();

            return $response;
        }

        throw new DomainNotFoundException("Category {$id} not found");
    }

    public function paginate(
        CategoryRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface {
        return new PaginatorPresenter($this->filter($filter)->paginate());
    }

    private function filter(?CategoryRepositoryFilter $filter)
    {
        $result = $this->model;

        if ($filter && ($filterResult = $filter->name) && !empty($filterResult)) {
            $result = $result->where('name', 'like', "%{$filterResult}%");
        }

        return $result->orderBy('name', 'asc');
    }
}
