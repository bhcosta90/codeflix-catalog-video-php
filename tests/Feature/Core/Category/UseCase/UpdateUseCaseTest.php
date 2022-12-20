<?php

namespace Tests\Feature\Core\Category\UseCase;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent as Repository;
use Core\Category\UseCase\UpdateUseCase as UseCase;
use Core\Category\UseCase\DTO\Update\Input;
use Tests\TestCase;

class UpdateUseCaseTest extends TestCase
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
            name: 'test',
            description: 'description',
            is_active: false
        ));

        $this->assertNotEmpty($response->id);
        $this->assertEquals('test', $response->name);
        $this->assertEquals('description', $response->description);
        $this->assertEquals(false, $response->is_active);
        $this->assertNotEmpty($response->created_at);

        $response = $this->useCase->execute(new Input(
            id: $model->id,
            name: 'test 2',
            description: 'description 2',
            is_active: true
        ));

        $this->assertEquals('test 2', $response->name);
        $this->assertEquals('description 2', $response->description);
        $this->assertEquals(true, $response->is_active);
        $this->assertNotEmpty($response->created_at);
    }
}
