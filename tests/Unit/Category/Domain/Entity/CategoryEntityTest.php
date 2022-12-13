<?php

namespace Tests\Unit\Category\Domain\Entity;

use Tests\Unit\TestCase;
use Core\Category\Domain\Entity\CategoryEntity;
use Shared\Domain\Entity\Exception\EntityValidationException;
use Throwable;

class CategoryEntityTest extends TestCase
{
    public function testAttributes()
    {
        $category = new CategoryEntity(
            name: 'Test',
            description: 'Desc',
            isActive: true,
        );

        $this->assertEquals('Test', $category->name);
        $this->assertEquals('Desc', $category->description);
        $this->assertTrue($category->isActive);
        $this->assertNotEmpty($category->id());
        $this->assertNotEmpty($category->createdAt());
    }

    public function testEnabled()
    {
        $category = new CategoryEntity(
            name: 'Test',
            isActive: false,
        );

        $this->assertFalse($category->isActive);
        $category->enabled();
        $this->assertTrue($category->isActive);
    }

    public function testDisabled()
    {
        $category = new CategoryEntity(
            name: 'Test',
            isActive: true,
        );

        $this->assertTrue($category->isActive);
        $category->disabled();
        $this->assertFalse($category->isActive);
    }

    public function testUpdated()
    {
        $id = 'b257aaca-75f2-4cdf-a96f-d438e1e891cc';

        $category = new CategoryEntity(
            name: 'Test',
            description: 'Desc',
            isActive: true,
            id: $id,
            createdAt: $date = '2020-01-01 00:00:00'
        );

        $category->update(
            name: 'Test 2',
            description: 'Desc 2',
        );

        $this->assertEquals('Test 2', $category->name);
        $this->assertEquals('Desc 2', $category->description);
        $this->assertEquals($id, $category->id());
        $this->assertEquals($date, $category->createdAt());

        $category->update(
            name: 'Test 3',
            description: 'Desc 2',
        );

        $this->assertEquals('Test 3', $category->name);
        $this->assertEquals('Desc 2', $category->description);

        $category->update(
            name: 'Test 3',
            description: null,
        );

        $this->assertEquals('Test 3', $category->name);
        $this->assertNull($category->description);
    }

    public function testExceptionName()
    {
        try {
            new CategoryEntity(
                name: 'Te',
                description: 'Desc',
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of category must be at least 2 characters', $e->getMessage());
        }

        try {
            new CategoryEntity(
                name: str_repeat('Te', 256),
                description: 'Desc',
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of category must be less than 255 characters', $e->getMessage());
        }

        try {
            $category = new CategoryEntity(
                name: 'Test',
                description: 'Desc',
                isActive: true,
            );
            $category->update(
                name: 'Te',
                description: 'Desc',
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of category must be at least 2 characters', $e->getMessage());
        }

        try {
            $category = new CategoryEntity(
                name: 'Test',
                description: 'Desc',
                isActive: true,
            );
            $category->update(
                name: str_repeat('Te', 256),
                description: 'Desc',
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of category must be less than 255 characters', $e->getMessage());
        }
    }

    public function testExceptionDescription()
    {
        try {
            new CategoryEntity(
                name: 'Test',
                description: str_repeat('D', 256),
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Description of category must be less than 255 characters', $e->getMessage());
        }

        try {
            $category = new CategoryEntity(
                name: 'Test',
                description: 'Desc',
                isActive: true,
            );
            $category->update(
                name: 'Test',
                description: str_repeat('De', 256),
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Description of category must be less than 255 characters', $e->getMessage());
        }
    }
}
