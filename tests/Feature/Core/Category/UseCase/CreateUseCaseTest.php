<?php

namespace Tests\Feature\Core\Category\UseCase;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent as Repository;
use Core\Category\UseCase\CreateUseCase as UseCase;
use Core\Category\UseCase\DTO\Create\{Input};
use Tests\TestCase;

class CreateUseCaseTest extends TestCase
{
    private UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UseCase(new Repository(new Model()));
    }

    public function testExec()
    {
        $response = $this->useCase->execute(new Input(
            name: 'test',
            description: 'description',
        ));

        $this->assertNotEmpty($response->id);
        $this->assertEquals('test', $response->name);
        $this->assertEquals('description', $response->description);

        $this->assertDatabaseHas('categories', [
            'id' => $response->id,
            'name' => 'test',
            'description' => 'description',
        ]);
    }
}
