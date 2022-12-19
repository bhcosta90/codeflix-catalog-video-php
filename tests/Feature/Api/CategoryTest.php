<?php

namespace Tests\Feature\Api;

use App\Models\Category as Model;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    private string $endpoint = '/api/categories';

    public function testListEmptyCategories()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
    }

    public function testListAllCategories()
    {
        Model::factory(30)->create();
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'last_page',
                'first_page',
                'current_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
    }

    public function testListPaginateCategories()
    {
        Model::factory(30)->create();
        $response = $this->getJson($this->endpoint . '?page=2');
        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.current_page'));
        $this->assertEquals(30, $response->json('meta.total'));
    }

    public function testListFilterCategories()
    {
        Model::factory(30)->create();
        Model::factory(5)->create(['name' => 'test']);
        $response = $this->getJson($this->endpoint . '?name=test');
        $response->assertStatus(200);
        $this->assertEquals(5, $response->json('meta.total'));
    }

    public function testListCategoryNotFound()
    {
        $response = $this->getJson($this->endpoint . '/fake-id');
        $response->assertStatus(404);
        $this->assertEquals('Category fake-id not found', $response->json('message'));
    }

    public function testListCategory()
    {
        $category = Model::factory()->create();
        $response = $this->getJson($this->endpoint . '/' . $category->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);

        $response->assertJson([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'is_active' => $category->is_active,
                'created_at' => $category->created_at,
            ]
        ]);
    }

    public function testValidationStore()
    {
        $response = $this->postJson($this->endpoint);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors',
        ]);
        $response->assertJson([
            'errors' => [
                'name' => [Lang::get('validation.required', ['attribute' => 'name'])]
            ]
        ]);

        $response = $this->postJson($this->endpoint, [
            'name' => 'a'
        ]);
        $response->assertJson([
            'errors' => [
                'name' => [Lang::get('validation.min.string', ['attribute' => 'name', 'min' => 3])]
            ]
        ]);

        $response = $this->postJson($this->endpoint, [
            'name' => str_repeat('a', 101)
        ]);

        $response->assertJson([
            'errors' => [
                'name' => [Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 100])]
            ]
        ]);
    }

    public function testStore()
    {
        $response = $this->postJson($this->endpoint, [
            'name' => 'test'
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $response->json('data.id'),
            'name' => $response->json('data.name'),
            'description' => $response->json('data.description'),
            'is_active' => $response->json('data.is_active'),
        ]);
    }

    public function testValidationUpdate()
    {
        $category = Model::factory()->create();
        $response = $this->putJson($this->endpoint . '/' . $category->id);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors',
        ]);
        $response->assertJson([
            'errors' => [
                'name' => [Lang::get('validation.required', ['attribute' => 'name'])],
                'is_active' => [Lang::get('validation.required', ['attribute' => 'is active'])],
            ]
        ]);
        $response = $this->putJson($this->endpoint . '/' . $category->id, [
            'name' => 'a',
            'is_active' => true,
        ]);
        $response->assertJson([
            'errors' => [
                'name' => [Lang::get('validation.min.string', ['attribute' => 'name', 'min' => 3])]
            ]
        ]);

        $response = $this->putJson($this->endpoint . '/' . $category->id, [
            'name' => str_repeat('a', 101),
            'is_active' => true,
        ]);

        $response->assertJson([
            'errors' => [
                'name' => [Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 100])]
            ]
        ]);
    }

    public function testUpdateNotFound(){
        $response = $this->putJson($this->endpoint . '/fake-id', [
            'name' => 'test',
            'is_active' => true,
        ]);
        $response->assertStatus(404);
        $this->assertEquals('Category fake-id not found', $response->json('message'));
    }

    public function testUpdate()
    {
        $category = Model::factory()->create();
        $response = $this->putJson($this->endpoint . '/' . $category->id, [
            'name' => 'test',
            'is_active' => false,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ]
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'test',
            'description' => null,
            'is_active' => false,
        ]);
    }

    public function testDestroyNotFound()
    {
        $response = $this->deleteJson($this->endpoint . '/fake-id');
        $response->assertStatus(404);
        $this->assertEquals('Category fake-id not found', $response->json('message'));
    }

    public function testDestroy()
    {
        $category = Model::factory()->create();
        $response = $this->deleteJson($this->endpoint . '/' . $category->id);
        $response->assertStatus(204);
        $this->assertEmpty($response->content());
        $this->assertSoftDeleted($category);
    }
}
