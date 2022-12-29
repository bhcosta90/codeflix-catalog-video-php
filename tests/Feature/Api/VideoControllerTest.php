<?php

namespace Tests\Feature\Api;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use Tests\TestCase;
use App\Models\Video as Model;
use Costa\DomainPackage\Tests\Traits\{TestResource, TestSave, TestValidation, TestUpload};
use Illuminate\Http\UploadedFile;
use App\Http\Resources\VideoResource as Resource;

class VideoControllerTest extends TestCase
{
    use TestValidation, TestResource, TestSave, TestUpload;

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

    public function testInvalidation()
    {
        $data = [
            'title' => '',
            'description' => '',
            'opened' => '',
            'duration' => '',
            'year_launched' => '',
            'rating' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'title' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'min.string', ['min' => 3]);
        $this->assertInvalidationInUpdateAction($data, 'min.string', ['min' => 3]);

        $data = [
            'duration' => 0,
            'year_launched' => 0,
        ];
        $this->assertInvalidationInStoreAction($data, 'min.numeric', ['min' => 1]);
        $this->assertInvalidationInUpdateAction($data, 'min.numeric', ['min' => 1]);

        $data = [
            'rating' => sha1(microtime()),
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');

        $this->assertInvalidationFile(
            'banner_file',
            'jpg',
            null,
            'image'
        );

        $this->assertInvalidationFile(
            'thumb_file',
            'jpg',
            null,
            'image'
        );

        $this->assertInvalidationFile(
            'thumb_half',
            'jpg',
            null,
            'image'
        );

        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            null,
            'mimetypes',
            ['values' => 'video/mp4']
        );

        $this->assertInvalidationFile(
            'trailer_file',
            'mp4',
            null,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testStore()
    {
        $data = [
            'title' => 'test',
            'description' => 'test',
            'rating' => 'L',
            'opened' => true,
            'duration' => 50,
            'year_launched' => 2020,
        ];

        $this->assertStore(
            $data + [
                'categories' => $this->categories,
                'genres' => $this->genres,
                'cast_members' => $this->castMembers,
            ],
            $data,
        );

        $this->assertDatabaseCount('category_video', 4);
        $this->assertDatabaseCount('genre_video', 4);
        $this->assertDatabaseCount('cast_member_video', 4);

        $data = [
            'title' => 'test',
            'description' => 'test',
            'rating' => 'L',
            'opened' => true,
            'duration' => 50,
            'year_launched' => 2020,
        ];

        $response = $this->assertStore(
            $data + ['banner_file' => UploadedFile::fake()->create('banner.jpg')],
            $data,
        );
        $id = $this->getIdFromResponse($response);
        $this->assertDatabaseHas('image_videos', [
            'video_id' => $id,
            'type' => 0,
        ]);

        $response = $this->assertStore(
            $data + ['thumb_file' => UploadedFile::fake()->create('banner.jpg')],
            $data,
        );
        $id = $this->getIdFromResponse($response);
        $this->assertDatabaseHas('image_videos', [
            'video_id' => $id,
            'type' => 1,
        ]);

        $response = $this->assertStore(
            $data + ['thumb_half' => UploadedFile::fake()->create('banner.jpg')],
            $data,
        );
        $id = $this->getIdFromResponse($response);
        $this->assertDatabaseHas('image_videos', [
            'video_id' => $id,
            'type' => 2,
        ]);

        $response = $this->assertStore(
            $data + ['trailer_file' => UploadedFile::fake()->create('trailler.mp4')],
            $data,
        );
        $id = $this->getIdFromResponse($response);
        $this->assertDatabaseHas('media_videos', [
            'video_id' => $id,
            'type' => 1,
            'media_status' => 2,
        ]);

        $response = $this->assertStore(
            $data + ['video_file' => UploadedFile::fake()->create('trailler.mp4')],
            $data,
        );
        $id = $this->getIdFromResponse($response);
        $this->assertDatabaseHas('media_videos', [
            'video_id' => $id,
            'type' => 0,
            'media_status' => 2,
        ]);
    }

    public function testUpdate()
    {
        $data = [
            'title' => 'test',
            'description' => 'test',
            'rating' => 'L',
            'opened' => true,
            'duration' => 50,
            'year_launched' => 2020,
        ];


        $this->categories[] = (string) Category::factory()->create()->id;
        $this->genres[] = (string) ($genre = Genre::factory()->create())->id;
        $this->castMembers[] = (string)CastMember::factory()->create()->id;
        $genre->categories()->attach($this->categories);

        $this->assertUpdate(
            $data + [
                'categories' => $this->categories,
                'genres' => $this->genres,
                'cast_members' => $this->castMembers,
            ],
            $data,
        );

        $this->assertDatabaseCount('category_video', 3);
        $this->assertDatabaseCount('genre_video', 3);
        $this->assertDatabaseCount('cast_member_video', 3);

        $this->assertUpdate(
            $data + ['banner_file' => UploadedFile::fake()->create('banner.jpg')],
            $data,
        );
        $this->assertDatabaseCount('image_videos', 1);

        $this->assertUpdate(
            $data + ['thumb_file' => UploadedFile::fake()->create('banner.jpg')],
            $data,
        );
        $this->assertDatabaseCount('image_videos', 2);

        $this->assertUpdate(
            $data + ['thumb_half' => UploadedFile::fake()->create('banner.jpg')],
            $data,
        );
        $this->assertDatabaseCount('image_videos', 3);

        $this->assertUpdate(
            $data + ['video_file' => UploadedFile::fake()->create('video.mp4')],
            $data,
        );
        $this->assertDatabaseCount('media_videos', 1);

        $this->assertUpdate(
            $data + ['trailer_file' => UploadedFile::fake()->create('video.mp4')],
            $data,
        );
        $this->assertDatabaseCount('media_videos', 2);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $this->model->id,
            'type' => 0,
        ]);
        $this->assertDatabaseHas('image_videos', [
            'video_id' => $this->model->id,
            'type' => 1,
        ]);

        $this->assertDatabaseHas('image_videos', [
            'video_id' => $this->model->id,
            'type' => 2,
        ]);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $this->model->id,
            'type' => 0,
            'media_status' => 2
        ]);

        $this->assertDatabaseHas('media_videos', [
            'video_id' => $this->model->id,
            'type' => 1,
            'media_status' => 2
        ]);
    }

    public function testShowNotFund()
    {
        $response = $this->getJson($this->endpoint . 'fake-id');
        $response->assertStatus(404);
        $this->assertEquals('Video fake-id not found', $response->json('message'));
    }

    public function testShow()
    {
        $response = $this->get($this->endpoint . $this->model->id);

        $this->serializedFields['categories'] = [];
        $this->serializedFields['genres'] = [];
        $this->serializedFields['cast_members'] = [];

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
    }
}
