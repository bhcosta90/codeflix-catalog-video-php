<?php

namespace Tests\Feature\Core\Video\UseCase;

use App\Factory\CastMemberFactory;
use App\Factory\CategoryFactory;
use App\Factory\GenreFactory;
use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent as Repository;
use App\Services\FileStorage;
use App\Services\VideoEventManager;
use App\Transactions\DatabaseTransaction;
use Core\Video\Domain\Event\VideoCreatedEvent;
use Core\Video\UseCase\CreateUseCase as UseCase;
use Core\Video\UseCase\DTO\Create as DTO;
use Exception;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use Throwable;

class CreateUseCaseTest extends TestCase
{
    protected UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();

        $this->useCase = new UseCase(
            repository: new Repository(new Model),
            transaction: new DatabaseTransaction,
            storage: new FileStorage(),
            eventManager: new VideoEventManager,
            categoryFactory: new CategoryFactory(new Category),
            genreFactory: new GenreFactory(new Genre, new Category),
            castMemberFactory: new CastMemberFactory(new CastMember)
        );
    }

    public function testCreate()
    {
        Event::fake([
            VideoCreatedEvent::class,
        ]);

        $response = $this->useCase->execute(new DTO\Input(
            title: 'test',
            description: 'description',
            yearLaunched: 2020,
            duration: 50,
            opened: true,
            rating: 'L'
        ));

        $this->assertNotEmpty($response->id);
        $this->assertNotEmpty($response->created_at);
        $this->assertEquals($response->title, 'test');
        $this->assertEquals($response->description, 'description');
        $this->assertEquals($response->year_launched, 2020);
        $this->assertEquals($response->duration, 50);
        $this->assertEquals($response->opened, 1);
        $this->assertEquals($response->rating, 'L');
        $this->assertEquals($response->categories, []);
        $this->assertEquals($response->genres, []);
        $this->assertEquals($response->cast_members, []);
        $this->assertEquals($response->thumb_file, null);
        $this->assertEquals($response->thumb_half, null);
        $this->assertEquals($response->banner_file, null);
        $this->assertEquals($response->trailer_file, null);
        $this->assertEquals($response->video_file, null);

        $this->assertDatabaseHas('videos', [
            'id' => $response->id,
            'title' => 'test',
            'description' => 'description',
            'year_launched' => 2020,
            'duration' => 50,
            'opened' => 1,
            'rating' => 'L',
        ]);

        Event::assertDispatched(VideoCreatedEvent::class);
    }

    public function testCreateWithRelation()
    {
        $categories = array_map(fn ($rs) => (string) $rs, Category::factory(2)->create()->pluck('id')->toArray());
        $genres = array_map(fn ($rs) => (string) $rs, ($genresAll = Genre::factory(2)->create())->pluck('id')->toArray());
        $castMembers = array_map(fn ($rs) => (string) $rs, CastMember::factory(2)->create()->pluck('id')->toArray());
        $genresAll->each(fn ($genre) => $genre->categories()->sync($categories));

        $response = $this->useCase->execute(new DTO\Input(
            title: 'test',
            description: 'description',
            yearLaunched: 2020,
            duration: 50,
            opened: true,
            rating: 'L',
            categories: $categories,
            genres: $genres,
            castMembers: $castMembers,
        ));

        $this->assertEquals($response->categories, $categories);
        $this->assertEquals($response->genres, $genres);
        $this->assertEquals($response->cast_members, $castMembers);

        $this->assertDatabaseHas('videos', [
            'id' => $response->id,
            'title' => 'test',
            'description' => 'description',
            'year_launched' => 2020,
            'duration' => 50,
            'opened' => 1,
            'rating' => 'L',
        ]);

        $this->assertDatabaseCount('category_video', 2);
        $this->assertDatabaseCount('genre_video', 2);
        $this->assertDatabaseCount('cast_member_video', 2);
    }

    public function testCreateVideoFile()
    {
        $fake = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $file = [
            'tmp_name' => $fake->getPathname(),
            'name' => $fake->getFilename(),
            'type' => $fake->getMimeType(),
            'error' => $fake->getError(),
        ];

        $response = $this->useCase->execute(new DTO\Input(
            title: 'test',
            description: 'description',
            yearLaunched: 2020,
            duration: 50,
            opened: true,
            rating: 'L',
            videoFile: $file,
        ));

        $this->assertNotEmpty($response->video_file);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $response->id,
            'type' => 0,
            'media_status' => 2,
        ]);
    }

    public function testCreateTrailerFile()
    {
        $fake = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $file = [
            'tmp_name' => $fake->getPathname(),
            'name' => $fake->getFilename(),
            'type' => $fake->getMimeType(),
            'error' => $fake->getError(),
        ];

        $response = $this->useCase->execute(new DTO\Input(
            title: 'test',
            description: 'description',
            yearLaunched: 2020,
            duration: 50,
            opened: true,
            rating: 'L',
            trailerFile: $file,
        ));

        $this->assertNotEmpty($response->trailer_file);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $response->id,
            'path' => $response->trailer_file,
            'type' => 1,
            'media_status' => 2,
        ]);
    }

    public function testCreateThumbFile()
    {
        $fake = UploadedFile::fake()->create('video.jpg', 1, 'image/jpeg');
        $file = [
            'tmp_name' => $fake->getPathname(),
            'name' => $fake->getFilename(),
            'type' => $fake->getMimeType(),
            'error' => $fake->getError(),
        ];

        $response = $this->useCase->execute(new DTO\Input(
            title: 'test',
            description: 'description',
            yearLaunched: 2020,
            duration: 50,
            opened: true,
            rating: 'L',
            thumbFile: $file,
        ));

        $this->assertNotEmpty($response->thumb_file);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $response->id,
            'path' => $response->thumb_file,
            'type' => 1,
        ]);
    }

    public function testCreateThumbHalf()
    {
        $fake = UploadedFile::fake()->create('video.jpg', 1, 'image/jpeg');
        $file = [
            'tmp_name' => $fake->getPathname(),
            'name' => $fake->getFilename(),
            'type' => $fake->getMimeType(),
            'error' => $fake->getError(),
        ];

        $response = $this->useCase->execute(new DTO\Input(
            title: 'test',
            description: 'description',
            yearLaunched: 2020,
            duration: 50,
            opened: true,
            rating: 'L',
            thumbHalf: $file,
        ));

        $this->assertNotEmpty($response->thumb_half);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $response->id,
            'path' => $response->thumb_half,
            'type' => 2,
        ]);
    }

    public function testCreateBannerFile()
    {
        $fake = UploadedFile::fake()->create('video.jpg', 1, 'image/jpeg');
        $file = [
            'tmp_name' => $fake->getPathname(),
            'name' => $fake->getFilename(),
            'type' => $fake->getMimeType(),
            'error' => $fake->getError(),
        ];

        $response = $this->useCase->execute(new DTO\Input(
            title: 'test',
            description: 'description',
            yearLaunched: 2020,
            duration: 50,
            opened: true,
            rating: 'L',
            bannerFile: $file,
        ));

        $this->assertNotEmpty($response->banner_file);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $response->id,
            'path' => $response->banner_file,
            'type' => 0,
        ]);
    }

    public function testExceptionCommit()
    {
        Event::listen(TransactionCommitted::class, function () {
            throw new Exception('commited');
        });

        $fake = UploadedFile::fake()->create('video.jpg', 1, 'image/jpeg');
        $file = [
            'tmp_name' => $fake->getPathname(),
            'name' => $fake->getFilename(),
            'type' => $fake->getMimeType(),
            'error' => $fake->getError(),
        ];

        $mock = Mockery::spy(FileStorage::class);
        $mock->shouldReceive('store')->andReturn($fake->getPathname());

        $useCase = new UseCase(
            repository: new Repository(new Model),
            transaction: new DatabaseTransaction,
            storage: $mock,
            eventManager: new VideoEventManager,
            categoryFactory: new CategoryFactory(new Category),
            genreFactory: new GenreFactory(new Genre, new Category),
            castMemberFactory: new CastMemberFactory(new CastMember)
        );

        try {
            $useCase->execute(new DTO\Input(
                title: 'test',
                description: 'description',
                yearLaunched: 2020,
                duration: 50,
                opened: true,
                rating: 'L',
                bannerFile: $file,
            ));
        } catch(Throwable $e) {
            $this->assertEquals('commited', $e->getMessage());
            $mock->shouldHaveReceived('delete')->times(1);
        }
    }
}
