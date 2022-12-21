<?php

namespace Tests\Feature\Core\Genre\UseCase;

use App\Factory\CategoryFactory;
use App\Models\Category;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent as Repository;
use App\Transactions\DatabaseTransaction;
use Core\Genre\UseCase\CreateUseCase as UseCase;
use Core\Genre\UseCase\DTO\Create\{Input};
use Core\Genre\UseCase\Exceptions\CategoryNotFound;
use Tests\TestCase;

class CreateUseCaseTest extends TestCase
{
    private UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UseCase(
            new Repository(new Model()),
            new CategoryFactory(new Category),
            new DatabaseTransaction(),
        );
    }

    public function testExec()
    {
        $response = $this->useCase->execute(new Input(
            name: 'test',
        ));

        $this->assertNotEmpty($response->id);
        $this->assertEquals('test', $response->name);

        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => 'test',
            'is_active' => true,
        ]);
    }

    public function testExecExceptionCategory()
    {
        $this->expectException(CategoryNotFound::class);
        $this->expectExceptionMessage('Categories not found');

        $this->useCase->execute(new Input(
            name: 'test',
            categories: ['123', '456']
        ));
    }

    public function testExecWithCategory()
    {
        $categories = array_map(fn($rs) => (string) $rs, Category::factory(3)->create()->pluck('id')->toArray());

        $response = $this->useCase->execute(new Input(
            name: 'test',
            categories: $categories
        ));

        $this->assertDatabaseHas('genres', [
            'id' => $response->id,
            'name' => 'test',
            'is_active' => true,
        ]);

        $this->assertDatabaseCount('category_genre', 3);
    }
}
