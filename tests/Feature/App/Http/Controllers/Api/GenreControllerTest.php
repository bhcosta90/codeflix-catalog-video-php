<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Factory\CategoryFactory;
use App\Http\Controllers\Api\GenreController as Controller;
use App\Http\Controllers\Api\GenreController;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreRepositoryEloquent as Repository;
use Core\Genre\UseCase;
use Illuminate\Http\Request;
use App\Http\Requests\Genre\StoreRequest;
use App\Http\Requests\Genre\UpdateRequest;
use App\Models\Category;
use App\Transactions\DatabaseTransaction;
use Core\Genre\UseCase\Exceptions\CategoryNotFound;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    private Repository $repository;
    private CategoryFactory $categoryFactory;
    private Controller $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new Repository(new Model);
        $this->controller = new GenreController();
        $this->categoryFactory = new CategoryFactory(new Category());
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
        ]));

        $response = $this->controller->store($request, new UseCase\CreateUseCase(
            $this->repository,
            $this->categoryFactory,
            new DatabaseTransaction
        ));
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->status());
    }

    public function testStoreExceptionCategories()
    {
        $this->expectException(CategoryNotFound::class);
        $this->expectExceptionMessage('Categories not found');

        $request = new StoreRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'test',
            'categories' => ['123', '456']
        ]));

        $this->controller->store($request, new UseCase\CreateUseCase(
            $this->repository,
            $this->categoryFactory,
            new DatabaseTransaction
        ));
    }

    public function testStoreWithCategories()
    {
        $categories = Category::factory(5)->create();

        $request = new StoreRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'test',
            'categories' => [
                (string) $categories[0]->id,
                (string) $categories[3]->id
            ]
        ]));

        $response = $this->controller->store($request, new UseCase\CreateUseCase(
            $this->repository,
            $this->categoryFactory,
            new DatabaseTransaction
        ));

        $this->assertDatabaseCount('genres', 1);
        $this->assertDatabaseCount('category_genre', 2);
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
            'is_active' => false,
        ]));

        $response = $this->controller->update($request, new UseCase\UpdateUseCase(
            $this->repository,
            $this->categoryFactory,
            new DatabaseTransaction
        ), $model->id);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());

        $this->assertDatabaseHas('genres', [
            'id' => $model->id,
            'name' => 'test',
            'is_active' => false,
        ]);
    }

    public function testUpdateExceptionCategories()
    {
        $this->expectException(CategoryNotFound::class);
        $this->expectExceptionMessage('Categories not found');

        $model = Model::factory()->create();
        $request = new UpdateRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'test',
            'is_active' => false,
            'categories' => ['123', '456']
        ]));

        $this->controller->update($request, new UseCase\UpdateUseCase(
            $this->repository,
            $this->categoryFactory,
            new DatabaseTransaction
        ), $model->id);
    }



    public function testUpdateWithCategories()
    {
        $categories = Category::factory(5)->create();
        $model = Model::factory()->create();
        $model->categories()->attach([
            (string) $categories[1]->id,
        ]);
        $this->assertDatabaseCount('category_genre', 1);
        $request = new UpdateRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'test',
            'is_active' => false,
            'categories' => [
                (string) $categories[0]->id,
                (string) $categories[3]->id
            ]
        ]));

        $response = $this->controller->update($request, new UseCase\UpdateUseCase(
            $this->repository,
            $this->categoryFactory,
            new DatabaseTransaction
        ), $model->id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());

        $this->assertDatabaseHas('genres', [
            'id' => $model->id,
            'name' => 'test',
            'is_active' => false,
        ]);
        $this->assertDatabaseCount('category_genre', 2);
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
