<?php

namespace Tests\Feature\App\Repositories\Eloquent\CastMemberRepositoryEloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\CastMember\Domain\Entity\CastMember as Entity;
use Core\CastMember\Domain\Enum\Type;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private CastMemberRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberRepositoryEloquent(new Model);
    }

    public function testUpdateNotFound()
    {
        $objModel = new Entity(name: 'test', type: Type::ACTOR);

        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('CastMember '.$objModel->id().' not found');

        $objModel->update(name: 'test', type: Type::DIRECTOR);
        $this->repository->update($objModel);
    }

    public function testUpdate()
    {
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);
        $objModel->update(name: 'test', type: Type::ACTOR);
        $this->repository->update($objModel);
        $this->assertDatabaseHas('cast_members', [
            'id' => $entity->id,
            'name' => 'test',
            'type' => 2,
        ]);
    }
}
