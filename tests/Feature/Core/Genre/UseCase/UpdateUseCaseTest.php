<?php

namespace Tests\Feature\Core\Genre\UseCase;

use App\Factory\CategoryFactory;
use App\Models\Category;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent as Repository;
use App\Transactions\DatabaseTransaction;
use Core\Genre\UseCase\DTO\Update\Input;
use Core\Genre\UseCase\Exceptions\CategoryNotFound;
use Core\Genre\UseCase\UpdateUseCase as UseCase;
use Tests\TestCase;

class UpdateUseCaseTest extends TestCase
{
    private UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UseCase(
            new Repository(new Model()),
            new CategoryFactory(new Category()),
            new DatabaseTransaction(),
        );
    }

    public function testExec()
    {
        $model = Model::factory()->create();

        $response = $this->useCase->execute(new Input(
            id: $model->id,
            name: 'test',
            is_active: false
        ));

        $this->assertNotEmpty($response->id);
        $this->assertEquals('test', $response->name);
        $this->assertEquals(false, $response->is_active);
        $this->assertNotEmpty($response->created_at);

        $response = $this->useCase->execute(new Input(
            id: $model->id,
            name: 'test 2',
            is_active: true
        ));

        $this->assertEquals('test 2', $response->name);
        $this->assertEquals(true, $response->is_active);
        $this->assertNotEmpty($response->created_at);
    }

    public function testExecExceptionCategory()
    {
        $this->expectException(CategoryNotFound::class);
        $this->expectExceptionMessage('Categories not found');

        $model = Model::factory()->create();

        $this->useCase->execute(new Input(
            id: $model->id,
            name: 'test',
            categories: ['123', '456']
        ));
    }

    public function testExecWithCategory()
    {
        $model = Model::factory()->create();
        $categories = array_map(fn ($rs) => (string) $rs, Category::factory(3)->create()->pluck('id')->toArray());

        $response = $this->useCase->execute(new Input(
            id: $model->id,
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
