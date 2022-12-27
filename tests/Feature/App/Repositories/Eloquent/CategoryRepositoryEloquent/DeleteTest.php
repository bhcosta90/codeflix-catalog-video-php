<?php

namespace Tests\Feature\App\Repositories\Eloquent\CategoryRepositoryEloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    private CategoryRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepositoryEloquent(new Model);
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
        $this->expectExceptionMessage('Category fake-id not found');

        $this->repository->delete('fake-id');
    }
}
