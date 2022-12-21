<?php

namespace Tests\Unit\Core\Genre\Domain\Entity;

use Tests\Unit\TestCase;
use Core\Genre\Domain\Entity\Genre;
use DateTime;
use Shared\Domain\Entity\Exception\EntityValidationException;
use Shared\ValueObject\Uuid;
use Throwable;

class GenreTest extends TestCase
{
    public function testAttributes()
    {
        $genre = new Genre(
            name: 'Test',
            isActive: true,
        );

        $this->assertEquals('Test', $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertNotEmpty($genre->id());
        $this->assertNotEmpty($genre->createdAt());
    }

    public function testEnabled()
    {
        $genre = new Genre(
            name: 'Test',
            isActive: false,
        );

        $this->assertFalse($genre->isActive);
        $genre->enabled();
        $this->assertTrue($genre->isActive);
    }

    public function testDisabled()
    {
        $genre = new Genre(
            name: 'Test',
            isActive: true,
        );

        $this->assertTrue($genre->isActive);
        $genre->disabled();
        $this->assertFalse($genre->isActive);
    }

    public function testUpdated()
    {
        $id = Uuid::random();

        $genre = new Genre(
            name: 'Test',
            isActive: true,
            id: $id,
            createdAt: new DateTime($date = '2020-01-01 00:00:00')
        );

        $genre->update(
            name: 'Test 2',
        );

        $this->assertEquals('Test 2', $genre->name);
        $this->assertEquals($id, $genre->id());
        $this->assertEquals($date, $genre->createdAt());

        $genre->update(
            name: 'Test 3',
        );

        $this->assertEquals('Test 3', $genre->name);

        $genre->update(
            name: 'Test 3',
        );

        $this->assertEquals('Test 3', $genre->name);
    }

    public function testExceptionName()
    {
        try {
            new Genre(
                name: 'Te',
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of genre must be at least 2 characters', $e->getMessage());
        }

        try {
            new Genre(
                name: str_repeat('Te', 256),
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of genre must be less than 255 characters', $e->getMessage());
        }
    }

    public function testAddCategoryInGenre()
    {
        $category = Uuid::random();
        $genre = new Genre(
            name: 'Test',
        );

        $this->assertCount(0, $genre->categories);
        $genre->addCategory(category: $category);
        $genre->addCategory(category: $category);
        $this->assertCount(2, $genre->categories);
    }

    public function testRemoveCategoryInGenre()
    {
        $category = Uuid::random();
        $category2 = Uuid::random();
        $genre = new Genre(
            name: 'Test',
            categories: [$category, $category2]
        );

        $this->assertCount(2, $genre->categories);
        $genre->subCategory(category: $category);
        $this->assertCount(1, $genre->categories);
        $this->assertEquals([$category2], array_values($genre->categories));
    }
}
