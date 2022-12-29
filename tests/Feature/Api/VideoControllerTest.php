<?php

namespace Tests\Feature\Api;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use Tests\TestCase;
use App\Models\Video as Model;
use Costa\DomainPackage\Tests\Traits\{TestResource, TestSave, TestValidation};

class VideoControllerTest extends TestCase
{
    use TestValidation, TestResource, TestSave;

    protected Model $model;
    protected array $categories;
    protected array $genres;
    protected array $castMembers;
    protected string $endpoint = '/api/videos/';

    protected $serializedFields = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'created_at',
        'video',
        'trailer',
        'banner',
        'thumb',
        'thumb_half',
        'categories' => [
            '*' => [
                'id',
                'name'
            ]
        ],
        'genres' => [
            '*' => [
                'id',
                'name'
            ]
        ],
        'cast_members' => [
            '*' => [
                'id',
                'name',
                'type'
            ]
        ],
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
        Model::factory(10)->create();
        $this->model = Model::factory()->create(['title' => 'test']);
        $this->categories = array_map(fn ($rs) => (string) $rs, (Category::factory(2)->create())->pluck('id')->toArray());
        $this->genres = array_map(fn ($rs) => (string) $rs, ($genre = Genre::factory(2)->create())->pluck('id')->toArray());
        $this->castMembers = array_map(fn ($rs) => (string) $rs, CastMember::factory(2)->create()->pluck('id')->toArray());
        $genre->each(fn ($genre) => $genre->categories()->sync($this->categories));
        $this->model->categories()->sync($this->categories);
        $this->model->genres()->sync($this->genres);
        $this->model->castMembers()->sync($this->castMembers);
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

        $response = $this->getJson($this->endpoint . '?title=test');
        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['total' => 1]
            ]);
    }
}
