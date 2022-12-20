<?php

namespace Tests\Feature\App\Repositories\Eloquent\CategoryRepositoryEloquent;

use App\Models\Category;
use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Category\Domain\Repository\CategoryRepositoryFilter;
use Shared\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class PaginateTest extends TestCase
{
    private CategoryRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepositoryEloquent(new Model);
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

    public function testPaginateWithFilterName()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['name' => 'test']);
        $response = $this->repository->paginate(new CategoryRepositoryFilter(name: 'test'));
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
