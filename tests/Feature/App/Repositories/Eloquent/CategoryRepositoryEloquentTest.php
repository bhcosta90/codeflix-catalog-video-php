<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Category\Domain\Entity\CategoryEntity as Entity;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class CategoryRepositoryEloquentTest extends TestCase
{
    private CategoryRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepositoryEloquent(new Model);
    }
    public function testInsert()
    {
        $entity = new Entity(
            name: 'test',
        );
        $this->repository->insert($entity);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('categories', [
            'id' => $entity->id,
            'name' => 'test',
        ]);
    }

    public function testExceptionFindById()
    {
        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Category fake-id not found');
        $this->repository->findById('fake-id');
    }

    public function testFindById()
    {
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);
        $this->assertInstanceOf(Entity::class, $objModel);
        $this->assertEquals($entity->id, $objModel->id());
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertCount(0, $response->items());
        $this->assertEquals(0, $response->total());
    }

    public function testFindAll()
    {
        Model::factory(10)->create();
        $response = $this->repository->findAll();
        $this->assertCount(10, $response->items());
        $this->assertEquals(10, $response->total());
    }
}
