<?php

namespace Tests\Feature\App\Repositories\Eloquent\GenreRepositoryEloquent;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Genre\Domain\Entity\Genre as Entity;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    private GenreRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreRepositoryEloquent(new Model);
    }

    public function testExceptionFindById()
    {
        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Genre fake-id not found');
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
