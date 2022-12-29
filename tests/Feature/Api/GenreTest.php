<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Genre as Model;
use App\Http\Resources\GenreResource as Resource;
use Illuminate\Database\Eloquent\Collection;
use Costa\DomainPackage\Tests\Traits\{TestResource, TestSave, TestValidation};
use Tests\TestCase;

class GenreTest extends TestCase
{
    use TestValidation, TestResource, TestSave;

    protected Model $model;
    protected Collection $categories;
    protected string $endpoint = '/api/genres/';

    protected $serializedFields = [
        'id',
        'name',
        'is_active',
        'created_at',
    ];

    protected function model()
    {
        return Model::class;
    }

    protected function routeStore()
    {
        return $this->endpoint;
    }

    protected function routeUpdate()
    {
        return $this->endpoint . $this->model->id;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\Authenticate::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
        $this->model = Model::factory()->create(['name' => 'test']);
        $this->categories = Category::factory(5)->create();
        $this->model->categories()->attach((string) $this->categories[4]->id);
    }

    public function testIndex()
    {
        $entities = Model::factory(14)->create();
        $entities[0]->categories()->attach([
            $this->categories[0]->id,
            $this->categories[3]->id,
        ]);
        $entities[1]->categories()->attach([
            $this->categories[1]->id,
            $this->categories[4]->id,
        ]);

        $response = $this->getJson($this->endpoint);
        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'meta' => [],
            ]);

        $response = $this->getJson($this->endpoint . '?name=test');
        $response->assertJson(['meta' => ['total' => 1]]);

        $url = http_build_query([
            'categories' => [(string) $this->categories[0]->id, (string) $this->categories[4]->id]
        ]);

        $response = $this->getJson($this->endpoint . "?{$url}");
        $response->assertJson(['meta' => ['total' => 3]]);

        $url = http_build_query([
            'name' => 'test',
            'categories' => [(string) $this->categories[0]->id, (string) $this->categories[4]->id]
        ]);
        $response = $this->getJson($this->endpoint . "?{$url}");
        $response->assertJson(['meta' => ['total' => 1]]);
    }

    public function testInvalidation(){
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 101),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 100]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 100]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

        $data = [
            'categories' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories' => ['a'],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $this->assertStore(
            $data,
            $data + ['is_active' => true]
        );
        $this->assertDatabaseCount('category_genre', 1);

        $this->assertStore(
            $data + [
                'categories' => [
                    (string) $this->categories[0]->id,
                    (string) $this->categories[4]->id,
                ]
            ],
            $data + ['is_active' => true]
        );
        $this->assertDatabaseCount('category_genre', 3);
    }

    public function testUpdate(){
        $data = [
            'name' => 'test',
            'is_active' => false
        ];
        $this->assertUpdate(
            $data,
            $data + ['is_active' => false]
        );
        $this->assertDatabaseCount('category_genre', 1);

        $this->assertUpdate(
            $data + [
                'categories' => [
                    (string) $this->categories[2]->id,
                    (string) $this->categories[3]->id,
                ]
            ],
            $data + ['is_active' => false]
        );
        $this->assertDatabaseCount('category_genre', 2);
    }

    public function testShowNotFund()
    {
        $response = $this->getJson($this->endpoint . 'fake-id');
        $response->assertStatus(404);
        $this->assertEquals('Genre fake-id not found', $response->json('message'));
    }

    public function testShow()
    {
        $response = $this->get($this->endpoint . $this->model->id);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

        $id = $response->json('data.id');
        $resource = new Resource(Model::find($id));
        $this->assertResource($response, $resource);
    }

    public function testDestroyNotFound()
    {
        $response = $this->deleteJson($this->endpoint . 'fake-id');
        $response->assertStatus(404);
        $this->assertEquals('Genre fake-id not found', $response->json('message'));
    }

    public function testDestroy()
    {
        $category = Model::factory()->create();
        $response = $this->deleteJson($this->endpoint . $category->id);
        $response->assertStatus(204);
        $this->assertEmpty($response->content());
        $this->assertSoftDeleted($category);
        $this->assertNotEmpty(Model::withTrashed()->find($category->id));
    }
}
