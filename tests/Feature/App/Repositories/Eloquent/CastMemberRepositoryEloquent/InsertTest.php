<?php

namespace Tests\Feature\App\Repositories\Eloquent\CastMemberRepositoryEloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberRepositoryEloquent;
use Core\CastMember\Domain\Entity\CastMember as Entity;
use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Tests\TestCase;

class InsertTest extends TestCase
{
    private CastMemberRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberRepositoryEloquent(new Model);
    }

    public function testInsert()
    {
        $entity = new Entity(
            name: 'test',
            type: Type::ACTOR
        );
        $this->repository->insert($entity);
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('cast_members', [
            'id' => $entity->id,
            'name' => 'test',
            'description' => 'description',
        ]);
    }

    public function testInsertDisabled()
    {
        $entity = new Entity(
            name: 'test',
            type: Type::ACTOR,
            isActive: false,
        );
        $this->repository->insert($entity);
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertDatabaseHas('cast_members', [
            'id' => $entity->id,
            'name' => 'test',
            'is_active' => false,
        ]);
    }
}
