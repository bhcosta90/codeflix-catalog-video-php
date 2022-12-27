<?php

namespace Tests\Feature\App\Repositories\Eloquent\VideoRepositoryEloquent;

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
