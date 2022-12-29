<?php

namespace Tests\Feature\App\Repositories\Eloquent\VideoRepositoryEloquent;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent;
use Core\Video\Domain\Entity\Video as Entity;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private VideoRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoRepositoryEloquent(new Model);
    }

    public function testUpdateNotFound()
    {
        $objModel = new Entity([
            'title' => 'test',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
            'categories' => [],
            'genres' => [],
            'castMembers' => [],
        ]);

        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Video '.$objModel->id().' not found');

        $objModel->update([
            'title' => 'test 2',
        ]);
        $this->repository->update($objModel);
    }

    public function testUpdate()
    {
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);
        $objModel->update([
            'title' => 'test 2',
        ]);
        $this->repository->update($objModel);
        $this->assertDatabaseHas('videos', [
            'id' => $entity->id,
            'title' => 'test 2',
        ]);
    }

    public function testUpdateWithRelation()
    {
        $category = array_map(fn ($rs) => (string) $rs, Category::factory(2)->create()->pluck('id')->toArray());
        $genre = array_map(fn ($rs) => (string) $rs, Genre::factory(1)->create()->pluck('id')->toArray());
        $castMember = array_map(fn ($rs) => (string) $rs, CastMember::factory(3)->create()->pluck('id')->toArray());
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);

        $objModel->update([
            'title' => 'test 2',
            'categories' => $category,
            'genres' => $genre,
            'castMembers' => $castMember,
        ]);
        $this->repository->update($objModel);
        $this->assertDatabaseHas('videos', [
            'id' => $entity->id,
            'title' => 'test 2',
        ]);

        $this->assertDatabaseCount('category_video', 2);
        $this->assertDatabaseCount('genre_video', 1);
        $this->assertDatabaseCount('cast_member_video', 3);
    }

    public function testUpdateThumFile()
    {
        $model = Model::factory()->create();
        $entity = $this->repository->findById($model->id);

        $entity->update([
            'thumbFile' => new Image('/tmp/fake'),
        ]);
        $this->repository->update($entity);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 1,
        ]);
    }

    public function testUpdateThumHalf()
    {
        $model = Model::factory()->create();
        $entity = $this->repository->findById($model->id);

        $entity->update([
            'thumbHalf' => new Image('/tmp/fake'),
        ]);
        $this->repository->update($entity);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 2,
        ]);
    }

    public function testUpdateBanner()
    {
        $model = Model::factory()->create();
        $entity = $this->repository->findById($model->id);

        $entity->update([
            'bannerFile' => new Image('/tmp/fake'),
        ]);
        $this->repository->update($entity);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 0,
        ]);
    }

    public function testUpdateTrailer()
    {
        $model = Model::factory()->create();
        $entity = $this->repository->findById($model->id);

        $entity->update([
            'trailerFile' => new Media('/tmp/fake'),
        ]);
        $this->repository->update($entity);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 1,
        ]);
    }

    public function testUpdateVideo()
    {
        $model = Model::factory()->create();
        $entity = $this->repository->findById($model->id);

        $entity->update([
            'videoFile' => new Media('/tmp/fake'),
        ]);
        $this->repository->update($entity);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 0,
        ]);
    }
}
