<?php

namespace Tests\Feature\App\Repositories\Eloquent\CastMemberRepositoryEloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\CastMember\Domain\Repository\CastMemberRepositoryFilter;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Tests\TestCase;

class FindAllTest extends TestCase
{
    private CastMemberRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberRepositoryEloquent(new Model);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(0, $response->items());
        $this->assertEquals(0, $response->total());
    }

    public function testFindAll()
    {
        Model::factory(10)->create();
        $response = $this->repository->findAll();
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(10, $response->items());
        $this->assertEquals(10, $response->total());
    }

    public function testFindAllWithFilterName()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['name' => 'test']);
        $response = $this->repository->findAll(new CastMemberRepositoryFilter(name: 'test', type: null));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
    }

    public function testFindAllWithFilterType()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['type' => 2]);
        $response = $this->repository->findAll(new CastMemberRepositoryFilter(name: null, type: 2));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
    }

    public function testFindAllWithFilterNameAndType()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['type' => 2]);
        Model::factory(7)->create(['name' => 'test', 'type' => 2]);
        $response = $this->repository->findAll(new CastMemberRepositoryFilter(name: 'test', type: 2));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(7, $response->items());
        $this->assertEquals(7, $response->total());
    }
}
