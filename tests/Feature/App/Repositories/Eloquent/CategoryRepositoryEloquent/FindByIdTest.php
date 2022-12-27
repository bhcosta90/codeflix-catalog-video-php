<?php

namespace Tests\Feature\App\Repositories\Eloquent\CategoryRepositoryEloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Core\Category\Domain\Entity\Category as Entity;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    private CategoryRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepositoryEloquent(new Model);
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
}
