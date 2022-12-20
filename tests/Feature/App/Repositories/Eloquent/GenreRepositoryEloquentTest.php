<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Genre\Domain\Entity\GenreEntity as Entity;
use Core\Genre\Domain\Repository\GenreRepositoryFilter;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Shared\Domain\Repository\ListInterface;
use Shared\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class GenreRepositoryEloquentTest extends TestCase
{
    private GenreRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreRepositoryEloquent(new Model);
    }

    public function testInsert()
    {
        $entity = new Entity(
            name: 'test',
        );
        $this->repository->insert($entity);
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('genres', [
            'id' => $entity->id,
            'name' => 'test',
        ]);
    }

    public function testInsertDisabled()
    {
        $entity = new Entity(
            name: 'test',
            isActive: false,
        );
        $this->repository->insert($entity);
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('genres', [
            'id' => $entity->id,
            'name' => 'test',
            'is_active' => false,
        ]);
    }

    public function testInsertWithCategories(){
        $categories = array_map(fn($rs) => (string) $rs, Category::factory(4)->create()->pluck('id')->toArray());
        $entity = new Entity(
            name: 'test',
            categories: $categories,
        );
        $this->repository->insert($entity);
        $this->assertDatabaseHas('genres', [
            'id' => $entity->id,
            'name' => 'test',
            'is_active' => true,
        ]);

        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testExceptionFindById()
    {
        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Genre fake-id not found');
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
        $response = $this->repository->findAll(new GenreRepositoryFilter(name: 'test', categories: []));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
    }

    public function testFindAllWithFilterCategories()
    {
        $categories = Category::factory(5)->create();
        Model::factory(10)->create()->each(function($obj) use($categories){
            $ids = [];
            foreach(array_rand($categories->toArray(), 3) as $id){
                array_push($ids, (string) $categories[$id]->id);
            }
            $obj->categories()->sync($ids);
        });

        $categoryFilter = Category::factory()->create();
        $entity = Model::factory()->create();
        $entity->categories()->attach((string) $categoryFilter->id);

        $response = $this->repository->findAll(new GenreRepositoryFilter(name: null, categories: [
            (string) $categoryFilter->id
        ]));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(1, $response->items());
        $this->assertEquals(1, $response->total());
    }

    public function testFindAllWithFilterNameAndCategories()
    {
        $categories = Category::factory(5)->create();
        Model::factory(10)->create()->each(function ($obj) use ($categories) {
            $ids = [];
            foreach (array_rand($categories->toArray(), 3) as $id) {
                array_push($ids, (string) $categories[$id]->id);
            }
            $obj->categories()->sync($ids);
        });

        $categoryFilter = Category::factory()->create();
        $entity = Model::factory()->create();
        $entity->categories()->attach((string) $categoryFilter->id);

        $entity = Model::factory()->create(['name' => 'test']);
        $entity->categories()->attach((string) $categoryFilter->id);

        $response = $this->repository->findAll(new GenreRepositoryFilter(name: 'test', categories: [
            (string) $categoryFilter->id
        ]));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(1, $response->items());
        $this->assertEquals(1, $response->total());
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
        $response = $this->repository->paginate(new GenreRepositoryFilter(name: 'test', categories: []));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(5, $response->items());
        $this->assertEquals(5, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(5, $response->from());
    }

    public function testUpdateNotFound()
    {
        $objModel = new Entity(name: 'test');

        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Genre ' . $objModel->id() . ' not found');

        $objModel->update(name: 'test');
        $this->repository->update($objModel);
    }

    public function testUpdate()
    {
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);
        $objModel->update(name: 'test');
        $this->repository->update($objModel);
        $this->assertDatabaseHas('genres', [
            'id' => $entity->id,
            'name' => 'test',
        ]);
    }

    public function testDelete()
    {
        $entity = Model::factory()->create();
        $this->repository->delete($entity->id);
        $this->assertSoftDeleted($entity);
    }

    public function testExceptionDelete()
    {
        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Genre fake-id not found');

        $this->repository->delete('fake-id');
    }
}
