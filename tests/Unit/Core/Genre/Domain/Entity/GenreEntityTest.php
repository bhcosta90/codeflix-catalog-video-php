<?php

namespace Tests\Unit\Core\Genre\Domain\Entity;

use Tests\Unit\TestCase;
use Core\Genre\Domain\Entity\GenreEntity;
use DateTime;
use Shared\Domain\Entity\Exception\EntityValidationException;
use Shared\ValueObject\Uuid;
use Throwable;

class GenreEntityTest extends TestCase
{
    public function testAttributes()
    {
        $genre = new GenreEntity(
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
        $genre = new GenreEntity(
            name: 'Test',
            isActive: false,
        );

        $this->assertFalse($genre->isActive);
        $genre->enabled();
        $this->assertTrue($genre->isActive);
    }

    public function testDisabled()
    {
        $genre = new GenreEntity(
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

        $genre = new GenreEntity(
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
            new GenreEntity(
                name: 'Te',
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of genre must be at least 2 characters', $e->getMessage());
        }

        try {
            new GenreEntity(
                name: str_repeat('Te', 256),
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of genre must be less than 255 characters', $e->getMessage());
        }
    }
}
