<?php

namespace Tests\Feature\App\Repositories\Eloquent\CastMemberRepositoryEloquent;

use App\Models\CastMember;
use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\CastMember\Domain\Repository\CastMemberRepositoryFilter;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class PaginateTest extends TestCase
{
    private CastMemberRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberRepositoryEloquent(new Model);
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
        $response = $this->repository->paginate(new CastMemberRepositoryFilter(name: 'test', type: null));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(5, $response->from());
    }

    public function testPaginateWithFilterType()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['type' => 2]);
        $response = $this->repository->paginate(new CastMemberRepositoryFilter(name: null, type: 2));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(5, $response->from());
    }

    public function testPaginateWithFilterNameAndType()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['type' => 2]);
        Model::factory(7)->create(['name' => 'test', 'type' => 2]);
        $response = $this->repository->paginate(new CastMemberRepositoryFilter(name: 'test', type: 2));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(7, $response->items());
        $this->assertEquals(7, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(7, $response->from());
    }
}
