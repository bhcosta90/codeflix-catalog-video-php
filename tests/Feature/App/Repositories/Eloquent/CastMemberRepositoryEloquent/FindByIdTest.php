<?php

namespace Tests\Feature\App\Repositories\Eloquent\CastMemberRepositoryEloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\CastMember\Domain\Entity\CastMemberEntity as Entity;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    private CastMemberRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberRepositoryEloquent(new Model);
    }

    public function testExceptionFindById()
    {
        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('CastMember fake-id not found');
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
