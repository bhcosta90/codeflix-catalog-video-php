<?php

namespace Tests\Feature\App\Repositories\Eloquent\GenreRepositoryEloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Genre\Domain\Repository\GenreRepositoryFilter;
use Costa\DomainPackage\Domain\Repository\ListInterface;
use Tests\TestCase;

class FindAllTest extends TestCase
{
    private GenreRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreRepositoryEloquent(new Model);
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
        $this->createEntityWithCategories();
        $response = $this->repository->findAll(new GenreRepositoryFilter(name: 'test', categories: []));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(1, $response->items());
        $this->assertEquals(1, $response->total());
    }

    public function testFindAllWithFilterCategories()
    {
        $data = $this->createEntityWithCategories();

        $response = $this->repository->findAll(new GenreRepositoryFilter(name: null, categories: [
            (string) $data['filter'],
        ]));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(2, $response->items());
        $this->assertEquals(2, $response->total());
    }

    public function testFindAllWithFilterNameAndCategories()
    {
        $data = $this->createEntityWithCategories();
        $response = $this->repository->findAll(new GenreRepositoryFilter(name: 'test', categories: [
            (string) $data['filter'],
        ]));
        $this->assertInstanceOf(ListInterface::class, $response);
        $this->assertCount(1, $response->items());
        $this->assertEquals(1, $response->total());
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
            ],
        ];
    }
}
