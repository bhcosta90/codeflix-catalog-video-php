<?php

namespace Tests\Feature\Core\Video\UseCase;

use App\Factory\CastMemberFactory;
use App\Factory\CategoryFactory;
use App\Factory\GenreFactory;
use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use Core\Video\UseCase\CreateUseCase as UseCase;
use Core\Video\UseCase\DTO\Create as DTO;
use App\Repositories\Eloquent\VideoRepositoryEloquent as Repository;
use App\Models\Video as Model;
use App\Models\Video;
use App\Services\FileStorage;
use App\Services\VideoEventManager;
use App\Transactions\DatabaseTransaction;
use Tests\TestCase;

class CreateUseCaseTest extends TestCase
{
    protected UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new UseCase(
            repository: new Repository(new Video),
            transaction: new DatabaseTransaction,
            storage: new FileStorage,
            eventManager: new VideoEventManager,
            categoryFactory: new CategoryFactory(new Category),
            genreFactory: new GenreFactory(new Genre, new Category),
            castMemberFactory: new CastMemberFactory(new CastMember)
        );
    }

    public function testCreate()
    {
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
        $this->assertEquals($response->title, "test");
        $this->assertEquals($response->description, "description");
        $this->assertEquals($response->year_launched, 2020);
        $this->assertEquals($response->duration, 50);
        $this->assertEquals($response->opened, 1);
        $this->assertEquals($response->rating, "L");
        $this->assertEquals($response->categories, []);
        $this->assertEquals($response->genres, []);
        $this->assertEquals($response->cast_members, []);
        $this->assertEquals($response->thumb_file, null);
        $this->assertEquals($response->thumb_half, null);
        $this->assertEquals($response->banner_file, null);
        $this->assertEquals($response->trailer_file, null);
        $this->assertEquals($response->video_file, null);
    }

    public function testCreateWithRelation()
    {
        $categories = array_map(fn ($rs) => (string) $rs, Category::factory(2)->create()->pluck('id')->toArray());
        $genres = array_map(fn ($rs) => (string) $rs, ($genresAll = Genre::factory(2)->create())->pluck('id')->toArray());
        $castMembers = array_map(fn ($rs) => (string) $rs, CastMember::factory(2)->create()->pluck('id')->toArray());
        $genresAll->each(fn($genre) => $genre->categories()->sync($categories));

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
    }
}
