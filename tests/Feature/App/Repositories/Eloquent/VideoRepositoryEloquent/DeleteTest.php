<?php

namespace Tests\Feature\App\Repositories\Eloquent\VideoRepositoryEloquent;

use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    private VideoRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoRepositoryEloquent(new Model);
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
        $this->expectExceptionMessage('Video fake-id not found');

        $this->repository->delete('fake-id');
    }
}
