<?php

namespace Tests\Feature\App\Models;

use App\Models\Category;
use App\Models\Genre;
use Tests\TestCase;

class GenreTest extends TestCase
{
    public function testCategories()
    {
        $genre = Genre::factory()->create();
        $categories = Category::factory(3)->create();
        $genre->categories()->sync([
            (string) $categories[0]->id
        ]);
        $this->assertCount(1, $genre->categories);
    }
}
