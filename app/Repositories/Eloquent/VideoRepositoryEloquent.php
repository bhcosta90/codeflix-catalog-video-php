<?php

namespace App\Repositories\Eloquent;

use App\Models\Video as VideoModel;
use App\Repositories\Presenters\{ListPresenter, PaginatorPresenter};
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Repository\VideoRepositoryFilter;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Costa\DomainPackage\Domain\Entity\Entity;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;

class VideoRepositoryEloquent implements VideoRepositoryInterface
{
    public function __construct(private VideoModel $model)
    {
        //
    }

    public function insert(Entity $entity): bool
    {
        $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'type' => $entity->type->value,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);

        return true;
    }

    public function update(Entity $entity): bool
    {
        if ($obj = $this->model->find($entity->id())) {
            return (bool) $obj->update([
                'name' => $entity->name,
                'type' => $entity->type->value,
                'is_active' => $entity->isActive,
            ]);
        }

        throw new DomainNotFoundException("Video {$entity->id()} not found");
    }

    public function delete(string $id): bool
    {
        if ($obj = $this->model->find($id)) {
            return $obj->delete();
        }

        throw new DomainNotFoundException("Video {$id} not found");
    }

    public function findAll(VideoRepositoryFilter $filter = null): ListInterface
    {
        return new ListPresenter($this->filter($filter)->get());
    }

    public function findById(string $id): ?Video
    {
        if ($obj = $this->model->find($id)) {
            return new Video([
                'title' => $obj->title,
                'description' => $obj->description,
                'yearLaunched' => $obj->yearLaunched,
                'duration' => $obj->duration,
                'opened' => $obj->opened,
                'rating' => $obj->rating,
                'categories' => $obj->categories()->pluck('id')->toArray(),
                'genres' => $obj->genres()->pluck('id')->toArray(),
                'castMembers' => $obj->castMembers()->pluck('id')->toArray(),
            ]);
        }

        throw new DomainNotFoundException("Video {$id} not found");
    }

    public function paginate(
        VideoRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface {
        return new PaginatorPresenter($this->filter($filter)->paginate());
    }

    private function filter(?VideoRepositoryFilter $filter)
    {
        $result = $this->model;

        if ($filter && ($filterResult = $filter->title) && !empty($filterResult)) {
            $result = $result->where('title', 'like', "%{$filterResult}%");
        }

        return $result->orderBy('title', 'asc');
    }

    public function updateMedia(Video $video): bool
    {
        return true;
    }
}
