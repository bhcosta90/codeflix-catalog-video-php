<?php

namespace Tests\Feature\App\Repositories\Eloquent\CastMemberRepositoryEloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    private CastMemberRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberRepositoryEloquent(new Model);
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
        $this->expectExceptionMessage('CastMember fake-id not found');

        $this->repository->delete('fake-id');
    }
}
