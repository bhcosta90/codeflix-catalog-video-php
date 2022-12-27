<?php

namespace Tests\Feature\App\Repositories\Eloquent\VideoRepositoryEloquent;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as Model;
use App\Repositories\Eloquent\VideoRepositoryEloquent;
use Core\Video\Domain\Entity\Video as Entity;
use Costa\DomainPackage\Domain\Repository\Exceptions\DomainNotFoundException;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    private VideoRepositoryEloquent $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoRepositoryEloquent(new Model);
    }

    public function testExceptionFindById()
    {
        $this->expectException(DomainNotFoundException::class);
        $this->expectExceptionMessage('Video fake-id not found');
        $this->repository->findById('fake-id');
    }

    public function testFindById()
    {
        $entity = Model::factory()->create();
        $objModel = $this->repository->findById($entity->id);
        $this->assertInstanceOf(Entity::class, $objModel);
        $this->assertEquals($entity->id, $objModel->id());
    }

    public function testFindByIdWithRelation(){
        $entity = Model::factory()->create();
        $category = Category::factory(2)->create()->pluck('id')->toArray();
        $genre = Genre::factory(1)->create()->pluck('id')->toArray();
        $castMember = CastMember::factory(3)->create()->pluck('id')->toArray();

        $entity->categories()->sync(array_map(fn($rs) => (string) $rs, $category));
        $entity->genres()->sync(array_map(fn($rs) => (string) $rs, $genre));
        $entity->castMembers()->sync(array_map(fn($rs) => (string) $rs, $castMember));

        $objModel = $this->repository->findById($entity->id);

        $this->assertEquals($objModel->categories, $category);
        $this->assertEquals($objModel->genres, $genre);
        $this->assertEquals($objModel->castMembers, $castMember);
    }
}
