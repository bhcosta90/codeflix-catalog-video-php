<?php

namespace Tests\Feature\Core\Category\UseCase;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent as Repository;
use Core\Category\UseCase\DeleteUseCase as UseCase;
use Shared\UseCase\DTO\Delete\{Input};
use Tests\TestCase;

class DeleteUseCaseTest extends TestCase
{
    private UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $model = new Model();
        $repository = new Repository($model);
        $this->useCase = new UseCase($repository);
    }

    public function testExec()
    {
        $model = Model::factory()->create();

        $response = $this->useCase->execute(new Input(
            id: $model->id,
        ));
        $this->assertTrue($response->success);
        $this->assertSoftDeleted($model);
    }
}
