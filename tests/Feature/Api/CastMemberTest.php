<?php

namespace Tests\Feature\Api;

use App\Http\Resources\CastMemberResource as Resource;
use App\Models\CastMember as Model;
use Costa\DomainPackage\Tests\Traits\{TestResource, TestSave, TestValidation};
use Tests\TestCase;

class CastMemberTest extends TestCase
{
    use TestValidation, TestResource, TestSave;

    protected Model $model;
    protected string $endpoint = '/api/cast_members/';

    protected $serializedFields = [
        'id',
        'name',
        'type',
        'is_active',
        'created_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\Authenticate::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
        $this->model = Model::factory()->create(['name' => 'test']);
    }

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

    public function testInvalidationData()
    {
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
            'type' => 0,
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testIndex()
    {
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

        $resource = Resource::collection(collect([$this->model]));
        $this->assertResource($response, $resource);
    }

    public function testIndexPagination()
    {
        Model::factory(20)->create();
        $response = $this->get($this->endpoint . '?page=2');
        $response->assertJson([
            'meta' => [
                'current_page' => 2,
                'total' => 21,
            ]
        ]);
    }

    public function testIndexFilter()
    {
        Model::factory(5)->create(['name' => 'testing']);
        Model::factory(5)->create(['type' => 2]);
        Model::factory(2)->create(['name' => 'test 123', 'type' => 2]);
        $response = $this->get($this->endpoint . '?name=test');
        $response->assertJson([
            'meta' => ['total' => 8]
        ]);


        $response = $this->get($this->endpoint . '?type=2');
        $response->assertJson([
            'meta' => ['total' => 7]
        ]);

        $response = $this->get($this->endpoint . '?type=2&name=test 123');
        $response->assertJson([
            'meta' => ['total' => 2]
        ]);
    }

    public function testShowNotFund()
    {
        $response = $this->getJson($this->endpoint . 'fake-id');
        $response->assertStatus(404);
        $this->assertEquals('CastMember fake-id not found', $response->json('message'));
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

    public function testStore()
    {
        $data = [
            'name' => 'test',
            'type' => 2,
        ];
        $response = $this->assertStore(
            $data,
            $data + ['is_active' => true]
        );
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);
    }

    public function testUpdateNotFound()
    {
        $response = $this->putJson($this->endpoint . 'fake-id', [
            'name' => 'test',
            'type' => 1,
            'is_active' => true,
        ]);
        $response->assertStatus(404);
        $this->assertEquals('CastMember fake-id not found', $response->json('message'));
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'test',
            'type' => 2,
            'is_active' => true
        ];
        $response = $this->assertUpdate($data, $data);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);
    }

    public function testDestroyNotFound()
    {
        $response = $this->deleteJson($this->endpoint . 'fake-id');
        $response->assertStatus(404);
        $this->assertEquals('CastMember fake-id not found', $response->json('message'));
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
