<?php

namespace Tests\Feature\App\Repositories\Eloquent\VideoRepositoryEloquent;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Tests\TestCase;

class InsertTest extends TestCase
{
    protected VideoRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoRepositoryEloquent(new Model);
    }

    public function testInsert()
    {
        $entity = new Video([
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

        $this->repository->insert($entity);
        $this->assertInstanceOf(VideoRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('videos', [
            'id' => $entity->id,
            'title' => 'test',
            'description' => 'description',
            'year_launched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => 'L',
        ]);

        $this->assertDatabaseCount('category_video', 0);
        $this->assertDatabaseCount('genre_video', 0);
        $this->assertDatabaseCount('cast_member_video', 0);
        $this->assertDatabaseCount('media_videos', 0);
        $this->assertDatabaseCount('image_videos', 0);
    }

    public function testInsertWithRelationships()
    {
        $category = array_map(fn ($rs) => (string) $rs, Category::factory(2)->create()->pluck('id')->toArray());
        $genre = array_map(fn ($rs) => (string) $rs, Genre::factory(1)->create()->pluck('id')->toArray());
        $castMember = array_map(fn ($rs) => (string) $rs, CastMember::factory(3)->create()->pluck('id')->toArray());

        $entity = new Video([
            'title' => 'test',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
            'categories' => $category,
            'genres' => $genre,
            'castMembers' => $castMember,
        ]);

        $this->repository->insert($entity);
        $this->assertDatabaseCount('category_video', 2);
        $this->assertDatabaseCount('genre_video', 1);
        $this->assertDatabaseCount('cast_member_video', 3);

        $this->assertEquals($entity->categories, $category);
        $this->assertEquals($entity->genres, $genre);
        $this->assertEquals($entity->castMembers, $castMember);
    }

    public function testInsertThumFile()
    {
        $entity = new Video([
            'title' => 'test',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
            'thumbFile' => new Image('/tmp/fake'),
        ]);

        $this->repository->insert($entity);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 1,
        ]);
    }

    public function testInsertThumHalf()
    {
        $entity = new Video([
            'title' => 'test',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
            'thumbHalf' => new Image('/tmp/fake'),
        ]);

        $this->repository->insert($entity);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 2,
        ]);
    }

    public function testInsertBanner()
    {
        $entity = new Video([
            'title' => 'test',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
            'bannerFile' => new Image('/tmp/fake'),
        ]);

        $this->repository->insert($entity);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 0,
        ]);
    }

    public function testInsertTrailler()
    {
        $entity = new Video([
            'title' => 'test',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
            'trailerFile' => new Media('/tmp/fake'),
        ]);

        $this->repository->insert($entity);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 1,
            'media_status' => 2,
        ]);
    }

    public function testInsertVideo()
    {
        $entity = new Video([
            'title' => 'test',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
            'videoFile' => new Media('/tmp/fake'),
        ]);

        $this->repository->insert($entity);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $entity->id(),
            'path' => '/tmp/fake',
            'type' => 0,
            'media_status' => 2,
        ]);
    }
}
