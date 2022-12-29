<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as GenreModel;
use App\Repositories\Presenters\ListPresenter;
use App\Repositories\Presenters\PaginatorPresenter;
use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryFilter;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;
use Costa\DomainPackage\ValueObject\Uuid;

class GenreRepositoryEloquent implements GenreRepositoryInterface
{
    public function __construct(private GenreModel $model)
    {
        //
    }

    public function insert(Genre $entity): bool
    {
        $genre = $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);

        if (count($entity->categories)) {
            $genre->categories()->sync($entity->categories);
        }

        return true;
    }

    public function update(Genre $entity): bool
    {
        if ($obj = $this->model->find($entity->id())) {
            $response = (bool) $obj->update([
                'name' => $entity->name,
                'is_active' => $entity->isActive,
            ]);

            if (count($entity->categories)) {
                $obj->categories()->sync($entity->categories);
            }

            return $response;
        }

        throw new DomainNotFoundException("Genre {$entity->id()} not found");
    }

    public function delete(string $id): bool
    {
        if ($obj = $this->model->find($id)) {
            return $obj->delete();
        }

        throw new DomainNotFoundException("Genre {$id} not found");
    }

    public function findAll(GenreRepositoryFilter $filter = null): ListInterface
    {
        return new ListPresenter($this->filter($filter)->get());
    }

    public function findById(string $id): ?Genre
    {
        if ($obj = $this->model->find($id)) {
            $response = new Genre(
                name: $obj->name,
                id: new Uuid($obj->id),
                createdAt: $obj->created_at,
            );

            ((bool) $obj->is_active) ? $response->enabled() : $response->disabled();

            return $response;
        }

        throw new DomainNotFoundException("Genre {$id} not found");
    }

    public function paginate(
        GenreRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface {
        return new PaginatorPresenter($this->filter($filter)->paginate($total, ['*'], 'page', $page));
    }

    private function filter(?GenreRepositoryFilter $filter)
    {
        $result = $this->model;

        if ($filter && ($filterResult = $filter->name) && ! empty($filterResult)) {
            $result = $result->where('name', 'like', "%{$filterResult}%");
        }

        if ($filter && ($filterResult = $filter->categories) && ! empty($filterResult)) {
            $result = $result->whereHas('categories', fn ($q) => $q->whereIn('id', $filterResult));
        }

        return $result->orderBy('name', 'asc');
    }
}
