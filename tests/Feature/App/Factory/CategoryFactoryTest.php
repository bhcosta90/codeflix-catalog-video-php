<?php

namespace Tests\Feature\App\Factory;

use App\Factory\CategoryFactory;
use App\Models\Category;
use App\Models\Genre;
use Tests\TestCase;

class CategoryFactoryTest extends TestCase
{
    private CategoryFactory $factory;

    private $categories;

    private $genres;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new CategoryFactory(new Category());

        $this->genres = Genre::factory(5)->create();
        $this->categories = Category::factory(5)->create();

        $this->genres[0]->categories()->attach([
            $this->categories[0]->id,
            $this->categories[3]->id,
        ]);

        $this->genres[1]->categories()->attach([
            $this->categories[1]->id,
            $this->categories[4]->id,
        ]);

        $this->genres[2]->categories()->attach([
            $this->categories[2]->id,
            $this->categories[3]->id,
        ]);
    }

    public function testFindByIdsWithGenres()
    {
        $ids = $this->factory->findByIdsWithGenres([
            $this->genres[0]->id,
        ], [
            $this->categories[0]->id,
            $this->categories[3]->id,
        ]);
        $this->assertCount(2, $ids);
    }

    public function testFindByIdsWithGenres2()
    {
        $ids = $this->factory->findByIdsWithGenres([
            $this->genres[0]->id,
        ], [
            $this->categories[0]->id,
        ]);
        $this->assertCount(1, $ids);
    }

    public function testFindByIdsWithGenres3()
    {
        $ids = $this->factory->findByIdsWithGenres([
            $this->genres[0]->id,
        ], [
            $this->categories[1]->id,
        ]);
        $this->assertCount(0, $ids);
    }
}
