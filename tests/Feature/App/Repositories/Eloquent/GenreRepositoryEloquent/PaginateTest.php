<?php

namespace Tests\Feature\App\Repositories\Eloquent\GenreRepositoryEloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Genre\Domain\Repository\GenreRepositoryFilter;
use Costa\DomainPackage\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class PaginateTest extends TestCase
{
    private GenreRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreRepositoryEloquent(new Model);
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

    public function testPaginateWithFilterCategory()
    {
        $data = $this->createEntityWithCategories();
        $response = $this->repository->paginate(new GenreRepositoryFilter(name: null, categories: [$data['filter']]));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(2, $response->items());
        $this->assertEquals(2, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(2, $response->from());
    }

    public function testPaginateWithFilterNameAndCategory()
    {
        $data = $this->createEntityWithCategories();
        $response = $this->repository->paginate(new GenreRepositoryFilter(name: 'test', categories: [$data['filter']]));
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(1, $response->items());
        $this->assertEquals(1, $response->total());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(1, $response->firstPage());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(1, $response->from());
    }

    private function createEntityWithCategories()
    {
        $categories = Category::factory(5)->create()->toArray();
        $entities = Model::factory(10)->create()->each(function ($obj) use ($categories) {
            $ids = [];
            foreach (array_rand($categories, 3) as $id) {
                array_push($ids, (string) $categories[$id]['id']);
            }
            $obj->categories()->sync($ids);
        });

        $categoryFilter = Category::factory()->create()->toArray();
        $entity = Model::factory()->create();
        $entity->categories()->attach((string) $categoryFilter['id']);

        $entity2 = Model::factory()->create(['name' => 'test']);
        $entity2->categories()->attach((string) $categoryFilter['id']);

        return [
            'entities' => $entities,
            'categories' => $categories,
            'filter' => $categoryFilter['id'],
            'entity' => [
                $entity->toArray(),
                $entity2->toArray(),
            ]
        ];
    }
}
