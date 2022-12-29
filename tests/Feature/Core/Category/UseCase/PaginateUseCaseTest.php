<?php

namespace Tests\Feature\Core\Category\UseCase;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent as Repository;
use Core\Category\Domain\Repository\CategoryRepositoryFilter;
use Core\Category\UseCase\DTO\Paginate\Input;
use Core\Category\UseCase\PaginateUseCase as UseCase;
use Tests\TestCase;

class PaginateUseCaseTest extends TestCase
{
    private UseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UseCase(new Repository(new Model()));
    }

    public function testExec()
    {
        Model::factory(10)->create();
        $response = $this->useCase->execute(new Input(
            page: 1,
            filter: null
        ));

        $this->assertCount(10, $response->items);
        $this->assertEquals(10, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(10, $response->from);
    }

    public function testExecFilter()
    {
        Model::factory(10)->create();
        Model::factory(5)->create(['name' => 'test']);
        $response = $this->useCase->execute(new Input(
            page: 1,
            filter: new CategoryRepositoryFilter(name: 'test')
        ));

        $this->assertCount(5, $response->items);
        $this->assertEquals(5, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(1, $response->to);
        $this->assertEquals(5, $response->from);
    }

    public function testExecEmpty()
    {
        $response = $this->useCase->execute(new Input(
            page: 1,
        ));

        $this->assertCount(0, $response->items);
        $this->assertEquals(0, $response->total);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(1, $response->last_page);
        $this->assertEquals(0, $response->to);
        $this->assertEquals(0, $response->from);
    }
}
