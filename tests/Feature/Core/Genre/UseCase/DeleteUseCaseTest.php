<?php

namespace Tests\Feature\Core\Genre\UseCase;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent as Repository;
use Core\Genre\UseCase\DeleteUseCase as UseCase;
use Shared\UseCase\DTO\Delete\{Input};
use Tests\TestCase;

class DeleteUseCaseTest extends TestCase
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
        $this->assertTrue($response->success);
        $this->assertSoftDeleted($model);
    }
}
