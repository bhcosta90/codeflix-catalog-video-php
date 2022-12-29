<?php

namespace Tests\Feature\App\Repositories\Eloquent\VideoRepositoryEloquent;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent;
use Core\Video\Domain\Repository\VideoRepositoryFilter;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class PaginateTest extends TestCase
{
    private VideoRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoRepositoryEloquent(new Model);
    }

    public function testRelations()
    {
        $categories = array_map(fn ($rs) => (string) $rs, Category::factory(1)->create()->pluck('id')->toArray());
        $genres = array_map(fn ($rs) => (string) $rs, Genre::factory(2)->create()->pluck('id')->toArray());
        $castMembers = array_map(fn ($rs) => (string) $rs, CastMember::factory(3)->create()->pluck('id')->toArray());
        $modelUpdate = Model::factory()->create();

        $modelUpdate->categories()->sync($categories);
        $modelUpdate->genres()->sync($genres);
        $modelUpdate->castMembers()->sync($castMembers);
        $modelUpdate->media()->create([
            'path' => '/tmp/test.txt',
            'media_status' => 2,
            'encoded_path' => null,
            'type' => 0,
        ]);
        $modelUpdate->trailer()->create([
            'path' => '/tmp/test.txt',
            'media_status' => 2,
            'encoded_path' => null,
            'type' => 1,
        ]);
        $modelUpdate->banner()->create([
            'path' => '/tmp/test.txt',
            'type' => 0,
        ]);
        $modelUpdate->thumb()->create([
            'path' => '/tmp/test.txt',
            'type' => 1,
        ]);
        $modelUpdate->thumbHalf()->create([
            'path' => '/tmp/test.txt',
            'type' => 2,
        ]);

        $response = $this->repository->paginate();

        $this->assertNotNull($response->items()[0]->media);
        $this->assertNotNull($response->items()[0]->trailer);
        $this->assertNotNull($response->items()[0]->banner);
        $this->assertNotNull($response->items()[0]->thumb);
        $this->assertNotNull($response->items()[0]->thumb_half);
        $this->assertCount(1, $response->items()[0]->categories);
        $this->assertCount(2, $response->items()[0]->genres);
        $this->assertCount(3, $response->items()[0]->cast_members);
    }

    public function testPaginate()
    {
        Model::factory(10)->create();
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(10, $response->items());
        $this->assertEquals(10, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(10, $response->from());
    }

    public function testPaginateWithFilterTitle()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['title' => 'test']);
        $response = $this->repository->paginate(new VideoRepositoryFilter(title: 'test'));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(5, $response->from());
    }
}
