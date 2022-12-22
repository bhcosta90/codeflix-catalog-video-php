<?php

namespace Tests\Feature\App\Repositories\Eloquent\CategoryRepositoryEloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Category\Domain\Entity\Category as Entity;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private CategoryRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepositoryEloquent(new Model);
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
}
