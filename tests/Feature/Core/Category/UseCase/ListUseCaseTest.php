<?php

namespace Tests\Feature\Core\Category\UseCase;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent as Repository;
use Core\Category\UseCase\ListUseCase as UseCase;
use Shared\UseCase\DTO\List\Input;
use Tests\TestCase;

class ListUseCaseTest extends TestCase
{
    private UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UseCase(new Repository(new Model()));
    }

    public function testExec()
    {
        $model = Model::factory()->create();

        $response = $this->useCase->execute(new Input(
            id: $model->id,
        ));

        $this->assertNotEmpty($response->id);
        $this->assertEquals($model->name, $response->name);
        $this->assertEquals($model->description, $response->description);
        $this->assertEquals($model->is_active, $response->is_active);
        $this->assertEquals($model->created_at, $response->created_at);
    }
}
