<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Factory\CastMemberFactory;
use App\Factory\CategoryFactory;
use App\Factory\GenreFactory;
use App\Http\Controllers\Api\VideoController as Controller;
use App\Http\Requests\Video\StoreRequest;
use App\Http\Requests\Video\UpdateRequest;
use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent as Repository;
use App\Services\FileStorage;
use App\Services\VideoEventManager;
use App\Transactions\DatabaseTransaction;
use Core\Video\UseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class VideoControllerTest extends TestCase
{
    protected Repository $repository;

    protected CategoryFactory $categoryFactory;

    protected GenreFactory $genreFactory;

    protected CastMemberFactory $castMemberFactory;

    protected Controller $controller;

    protected array $categories;

    protected array $genres;

    protected array $castMembers;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        $this->repository = new Repository(new Model);
        $this->controller = new Controller();
        $this->categoryFactory = new CategoryFactory(new Category());
        $this->genreFactory = new GenreFactory(new Genre(), new Category());
        $this->castMemberFactory = new CastMemberFactory(new CastMember());

        $this->categories = array_map(
            fn ($rs) => (string) $rs,
            (Category::factory(2)->create())->pluck('id')->toArray()
        );
        $this->genres = array_map(
            fn ($rs) => (string) $rs,
            ($genre = Genre::factory(2)->create())->pluck('id')->toArray()
        );
        $this->castMembers = array_map(
            fn ($rs) => (string) $rs,
            CastMember::factory(2)->create()->pluck('id')->toArray()
        );
        $genre->each(fn ($genre) => $genre->categories()->sync($this->categories));
    }

    public function testIndex()
    {
        $response = $this->controller->index(new Request(), new UseCase\PaginateUseCase($this->repository));
        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $mediaVideoFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');

        $request = new StoreRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'title' => 'test',
            'description' => 'test',
            'year_launched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => 'L',
            'cast_members' => $this->castMembers,
            'genres' => $this->genres,
            'categories' => $this->categories,
            'video_file' => $mediaVideoFile,
            'trailer_file' => $mediaVideoFile,
            'banner_file' => $mediaVideoFile,
            'thumb_file' => $mediaVideoFile,
            'thumb_half' => $mediaVideoFile,
        ]));

        $response = $this->controller->store($request, new UseCase\CreateUseCase(
            repository: $this->repository,
            transaction: new DatabaseTransaction,
            storage: new FileStorage,
            eventManager: new VideoEventManager,
            categoryFactory: $this->categoryFactory,
            genreFactory: $this->genreFactory,
            castMemberFactory: $this->castMemberFactory,
        ));
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->status());
        $this->assertEquals($this->castMembers, $response->original->cast_members);
        $this->assertEquals($this->genres, $response->original->genres);
        $this->assertEquals($this->categories, $response->original->categories);
        $this->assertNotNull($response->original->video_file);
        $this->assertNotNull($response->original->trailer_file);
        $this->assertNotNull($response->original->banner_file);
        $this->assertNotNull($response->original->thumb_file);
        $this->assertNotNull($response->original->thumb_half);

        $this->assertDatabaseCount('category_video', 2);
        $this->assertDatabaseCount('genre_video', 2);
        $this->assertDatabaseCount('cast_member_video', 2);
    }

    public function testShow()
    {
        $model = Model::factory()->create();
        $response = $this->controller->show(new UseCase\ListUseCase($this->repository), $model->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
    }

    public function testUpdate()
    {
        $mediaVideoFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $model = Model::factory()->create();
        $request = new UpdateRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'title' => 'test',
            'description' => 'test',
            'year_launched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => 'L',
            'cast_members' => $this->castMembers,
            'genres' => $this->genres,
            'categories' => $this->categories,
            'video_file' => $mediaVideoFile,
            'trailer_file' => $mediaVideoFile,
            'banner_file' => $mediaVideoFile,
            'thumb_file' => $mediaVideoFile,
            'thumb_half' => $mediaVideoFile,
        ]));

        $response = $this->controller->update($request, new UseCase\UpdateUseCase(
            repository: $this->repository,
            transaction: new DatabaseTransaction,
            storage: new FileStorage,
            eventManager: new VideoEventManager,
            categoryFactory: $this->categoryFactory,
            genreFactory: $this->genreFactory,
            castMemberFactory: $this->castMemberFactory,
        ), $model->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($this->castMembers, $response->original->cast_members);
        $this->assertEquals($this->genres, $response->original->genres);
        $this->assertEquals($this->categories, $response->original->categories);

        $this->assertNotNull($response->original->video_file);
        $this->assertNotNull($response->original->trailer_file);
        $this->assertNotNull($response->original->banner_file);
        $this->assertNotNull($response->original->thumb_file);
        $this->assertNotNull($response->original->thumb_half);

        $this->assertDatabaseHas('videos', [
            'id' => $model->id,
            'title' => 'test',
            'description' => 'test',
            'year_launched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => 'L',
        ]);

        $this->assertDatabaseCount('category_video', 2);
        $this->assertDatabaseCount('genre_video', 2);
        $this->assertDatabaseCount('cast_member_video', 2);
    }

    public function testDelete()
    {
        $model = Model::factory()->create();
        $response = $this->controller->destroy(new UseCase\DeleteUseCase($this->repository), $model->id);
        $this->assertEmpty($response->content());
        $this->assertEquals(204, $response->status());

        $this->assertSoftDeleted($model);
    }
}
