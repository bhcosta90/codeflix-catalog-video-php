<?php

namespace Tests\Feature\App\Repositories\Eloquent\GenreRepositoryEloquent;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent;
use Core\Genre\Domain\Entity\Genre as Entity;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private GenreRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreRepositoryEloquent(new Model);
    }

    public function testUpdateNotFound()
    {
        $objModel = new Entity(name: 'test');

        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Genre ' . $objModel->id() . ' not found');

        $objModel->update(name: 'test');
        $this->repository->update($objModel);
    }

    public function testUpdate()
    {
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);
        $objModel->update(name: 'test');
        $this->repository->update($objModel);
        $this->assertDatabaseHas('genres', [
            'id' => $entity->id,
            'name' => 'test',
        ]);
    }
}
