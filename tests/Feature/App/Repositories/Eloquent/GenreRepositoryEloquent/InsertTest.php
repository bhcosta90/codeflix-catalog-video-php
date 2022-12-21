<?php

namespace Tests\Feature\App\Repositories\Eloquent\GenreRepositoryEloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Genre\Domain\Entity\Genre as Entity;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Tests\TestCase;

class InsertTest extends TestCase
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

    public function testInsertWithCategories()
    {
        $categories = array_map(fn ($rs) => (string) $rs, Category::factory(4)->create()->pluck('id')->toArray());
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
}
