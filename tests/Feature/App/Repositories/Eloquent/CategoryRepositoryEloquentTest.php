<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Category\Domain\Entity\CategoryEntity as Entity;
use Core\Category\Domain\Repository\CategoryRepositoryFilter;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Shared\Domain\Repository\ListInterface;
use Shared\Domain\Repository\PaginationInterface;
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

    public function testFindAllWithFilter()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['name' => 'test']);
        $response = $this->repository->findAll(new CategoryRepositoryFilter(name: 'test'));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
    }

    public function testPaginateEmpty()
    {
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
        $this->assertEquals(0, $response->total());
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

    public function testPaginateWithFilter()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['name' => 'test']);
        $response = $this->repository->paginate(new CategoryRepositoryFilter(name: 'test'));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
    }

    public function testUpdateNotFound()
    {
        $objModel = new Entity(name: 'test');

        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Category ' . $objModel->id() . ' not found');

        $objModel->update(name: 'test', description: 'description');
        $this->repository->update($objModel);
    }

    public function testUpdate()
    {
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);
        $objModel->update(name: 'test', description: 'description');
        $this->repository->update($objModel);
        $this->assertDatabaseHas('categories', [
            'id' => $entity->id,
            'name' => 'test',
            'description' => 'description',
        ]);
    }

    public function testDelete()
    {
        $entity = Model::factory()->create();
        $this->repository->delete($entity->id);
        $this->assertDatabaseMissing('categories', [
            'id' => $entity->id,
            'deleted_at' => null,
        ]);
    }

    public function testExceptionDelete()
    {
        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Category fake-id not found');

        $this->repository->delete('fake-id');
    }
}
