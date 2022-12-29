<?php

namespace Tests\Feature\App\Repositories\Eloquent\CategoryRepositoryEloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Category\Domain\Entity\Category as Entity;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Tests\TestCase;

class InsertTest extends TestCase
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
            description: 'description'
        );
        $this->repository->insert($entity);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('categories', [
            'id' => $entity->id,
            'name' => 'test',
            'description' => 'description',
        ]);
    }

    public function testInsertDisabled()
    {
        $entity = new Entity(
            name: 'test',
            isActive: false,
        );
        $this->repository->insert($entity);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('categories', [
            'id' => $entity->id,
            'name' => 'test',
            'is_active' => false,
        ]);
    }
}
