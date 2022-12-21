<?php

namespace Tests\Feature\Core\Genre\UseCase;

use App\Models\Category;
use Tests\TestCase;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent as Repository;
use Core\Genre\Domain\Repository\GenreRepositoryFilter;
use Core\Genre\UseCase\PaginateUseCase as UseCase;
use Core\Genre\UseCase\DTO\Paginate\Input;

class PaginateUseCaseTest extends TestCase
{
    private UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UseCase(new Repository(new Model()));
    }

    public function testExec()
    {
        Model::factory(10)->create();
        $response = $this->useCase->execute(new Input(
            page: 1,
            filter: null
        ));

        $this->assertCount(10, $response->items);
        $this->assertEquals(10, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(10, $response->from);
    }

    public function testExecFilterName()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['name' => 'test']);
        $response = $this->useCase->execute(new Input(
            page: 1,
            filter: new GenreRepositoryFilter(name: 'test', categories: [])
        ));

        $this->assertCount(5, $response->items);
        $this->assertEquals(5, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(5, $response->from);
    }

    public function testExecFilterCategory()
    {
        $entities = Model::factory(10)->create();
        $category = Category::factory()->create();
        $entities->first()->categories()->attach([$category->id]);

        $response = $this->useCase->execute(new Input(
            page: 1,
            filter: new GenreRepositoryFilter(name: null, categories: [(string) $category->id])
        ));

        $this->assertCount(1, $response->items);
        $this->assertEquals(1, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(1, $response->from);
    }

    public function testExecFilterNameAndCategory()
    {
        $entities = Model::factory(10)->create();
        $entitiesName = Model::factory(5)->create(['name' => 'test']);
        $category = Category::factory()->create();
        $entities->first()->categories()->attach([(string) $category->id]);
        $entitiesName[2]->categories()->attach([(string) $category->id]);

        $response = $this->useCase->execute(new Input(
            page: 1,
            filter: new GenreRepositoryFilter(name: 'test', categories: [(string) $category->id])
        ));

        $this->assertCount(1, $response->items);
        $this->assertEquals(1, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(1, $response->from);
    }

    public function testExecEmpty(){
        $response = $this->useCase->execute(new Input(
            page: 1,
        ));

        $this->assertCount(0, $response->items);
        $this->assertEquals(0, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(0, $response->to);
        $this->assertEquals(0, $response->from);
    }
}
