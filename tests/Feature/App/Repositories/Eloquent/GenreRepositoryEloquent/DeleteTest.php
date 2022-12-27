<?php

namespace Tests\Feature\App\Repositories\Eloquent\GenreRepositoryEloquent;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    private GenreRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreRepositoryEloquent(new Model);
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
