<?php

namespace Tests\Feature\App\Repositories\Eloquent\VideoRepositoryEloquent;

use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent;
use Core\Video\Domain\Repository\VideoRepositoryFilter;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Tests\TestCase;

class FindAllTest extends TestCase
{
    private VideoRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoRepositoryEloquent(new Model);
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

    public function testFindAllWithFilterTitle()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['title' => 'test']);
        $response = $this->repository->findAll(new VideoRepositoryFilter(title: 'test'));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
    }
}
