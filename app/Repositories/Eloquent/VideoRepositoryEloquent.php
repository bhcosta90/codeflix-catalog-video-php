<?php

namespace App\Repositories\Eloquent;

use App\Models\Video as VideoModel;
use App\Repositories\Presenters\ListPresenter;
use App\Repositories\Presenters\PaginatorPresenter;
use Core\Video\Builder\VideoUpdateBuilder;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryFilter;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Interfaces\VideoBuilderInterface;
use Costa\DomainPackage\Domain\Entity\Entity;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;

class VideoRepositoryEloquent implements VideoRepositoryInterface
{
    private VideoBuilderInterface $builder;

    use Trait\VideoTrait;

    public function __construct(private VideoModel $model)
    {
        $this->builder = new VideoUpdateBuilder();
    }

    public function insert(Entity $entity): bool
    {
        $db = $this->model->create([
            'id' => $entity->id(),
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'rating' => $entity->rating->value,
            'duration' => $entity->duration,
            'opened' => $entity->opened,
        ]);

        $this->syncRelationships($db, $entity);
        $this->updateImageThumb($entity, $db);
        $this->updateImageThumbHalf($entity, $db);
        $this->updateImageBanner($entity, $db);
        $this->updateMediaVideo($entity, $db);
        $this->updateMediaTrailer($entity, $db);

        return (bool) $db;
    }

    public function update(Entity $entity): bool
    {
        if ($db = $this->model->find($entity->id())) {
            $response = (bool) $db->update([
                'title' => $entity->title,
                'description' => $entity->description,
                'year_launched' => $entity->yearLaunched,
                'rating' => $entity->rating->value,
                'duration' => $entity->duration,
                'opened' => $entity->opened,
            ]);

            $this->syncRelationships($db, $entity);
            $this->updateImageThumb($entity, $db);
            $this->updateImageThumbHalf($entity, $db);
            $this->updateImageBanner($entity, $db);
            $this->updateMediaVideo($entity, $db);
            $this->updateMediaTrailer($entity, $db);

            return $response;
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
            $obj->categories = $obj->categories->pluck('id')->toArray();
            $obj->genres = $obj->genres->pluck('id')->toArray();
            $obj->castMembers = $obj->castMembers->pluck('id')->toArray();
            $obj->rating = Rating::from($obj->rating);

            return $this->builder->createEntity($obj)->getEntity();
        }

        throw new DomainNotFoundException("Video {$id} not found");
    }

    /**
     * @param  VideoRepositoryFilter|null  $filter
     * @param  int  $page
     * @param  int  $total
     * @return PaginationInterface
     */
    public function paginate(
        object $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface {
        return new PaginatorPresenter($this->filter($filter)->paginate($total, ['*'], 'page', $page));
    }

    private function filter(?VideoRepositoryFilter $filter)
    {
        $result = $this->model;

        if ($filter && ($filterResult = $filter->title) && ! empty($filterResult)) {
            $result = $result->where('title', 'like', "%{$filterResult}%");
        }

        return $result->with([
            'media',
            'trailer',
            'banner',
            'thumb',
            'thumbHalf',
            'categories',
            'castMembers',
            'genres',
        ])->orderBy('title', 'asc');
    }

    public function updateMedia(Video $video): bool
    {
        if (! $objectModel = $this->model->find($video->id())) {
            throw new NotFoundException('Video not found');
        }

        $this->updateMediaVideo($video, $objectModel);
        $this->updateMediaTrailer($video, $objectModel);

        $this->updateImageBanner($video, $objectModel);
        $this->updateImageThumb($video, $objectModel);
        $this->updateImageThumbHalf($video, $objectModel);

        return true;
    }

    protected function syncRelationships(VideoModel $model, Entity $entity)
    {
        $model->categories()->sync($entity->categories);
        $model->genres()->sync($entity->genres);
        $model->castMembers()->sync($entity->castMembers);
    }
}
