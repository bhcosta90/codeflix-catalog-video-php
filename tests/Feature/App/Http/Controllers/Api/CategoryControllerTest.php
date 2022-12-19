<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController as Controller;
use App\Http\Controllers\Api\CategoryController;
use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryRepositoryEloquent as Repository;
use Core\Category\UseCase;
use Core\Category\UseCase\DTO;
use Illuminate\Http\Request;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    private Repository $repository;
    private Controller $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new Repository(new Model);
        $this->controller = new CategoryController();
    }

    public function testIndex()
    {
        $response = $this->controller->index(new Request(), new UseCase\PaginateUseCase($this->repository));

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $request = new StoreRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'test',
            'description' => 'description'
        ]));

        $response = $this->controller->store($request, new UseCase\CreateUseCase($this->repository));
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->status());
    }

    public function testShow()
    {
        $model = Model::factory()->create();
        $response = $this->controller->show(new UseCase\ListUseCase($this->repository), $model->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
    }

    public function testUpdate()
    {
        $model = Model::factory()->create();
        $request = new UpdateRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'test',
            'description' => 'description',
            'is_active' => false,
        ]));

        $response = $this->controller->update($request, new UseCase\UpdateUseCase($this->repository), $model->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());

        $this->assertDatabaseHas('categories', [
            'id' => $model->id,
            'name' => 'test',
            'description' => 'description',
            'is_active' => false,
        ]);
    }

    public function testDelete()
    {
        $model = Model::factory()->create();
        $response = $this->controller->destroy(new UseCase\DeleteUseCase($this->repository), $model->id);
        $this->assertEmpty($response->content());
        $this->assertEquals(204, $response->status());

        $this->assertSoftDeleted($model);
    }
}
