<?php

namespace Tests\Unit\Core\Category\Domain\Entity;

use Tests\Unit\TestCase;
use Core\Category\Domain\Entity\CategoryEntity;
use Shared\Domain\Entity\Exception\EntityValidationException;
use Throwable;

class CategoryEntityTest extends TestCase
{
    public function testAttributes()
    {
        $entity = new CategoryEntity(
            name: 'Test',
            description: 'Desc',
            isActive: true,
        );

        $this->assertEquals('Test', $entity->name);
        $this->assertEquals('Desc', $entity->description);
        $this->assertTrue($entity->isActive);
        $this->assertNotEmpty($entity->id());
        $this->assertNotEmpty($entity->createdAt());
    }

    public function testEnabled()
    {
        $entity = new CategoryEntity(
            name: 'Test',
            isActive: false,
        );

        $this->assertFalse($entity->isActive);
        $entity->enabled();
        $this->assertTrue($entity->isActive);
    }

    public function testDisabled()
    {
        $entity = new CategoryEntity(
            name: 'Test',
            isActive: true,
        );

        $this->assertTrue($entity->isActive);
        $entity->disabled();
        $this->assertFalse($entity->isActive);
    }

    public function testUpdated()
    {
        $id = 'b257aaca-75f2-4cdf-a96f-d438e1e891cc';

        $entity = new CategoryEntity(
            name: 'Test',
            description: 'Desc',
            isActive: true,
            id: $id,
            createdAt: $date = '2020-01-01 00:00:00'
        );

        $entity->update(
            name: 'Test 2',
            description: 'Desc 2',
        );

        $this->assertEquals('Test 2', $entity->name);
        $this->assertEquals('Desc 2', $entity->description);
        $this->assertEquals($id, $entity->id());
        $this->assertEquals($date, $entity->createdAt());

        $entity->update(
            name: 'Test 3',
            description: 'Desc 2',
        );

        $this->assertEquals('Test 3', $entity->name);
        $this->assertEquals('Desc 2', $entity->description);

        $entity->update(
            name: 'Test 3',
            description: null,
        );

        $this->assertEquals('Test 3', $entity->name);
        $this->assertNull($entity->description);
    }

    public function testExceptionNameAndDescription()
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
            $entity = new CategoryEntity(
                name: 'Test',
                description: 'Desc',
                isActive: true,
            );
            $entity->update(
                name: 'Te',
                description: 'Desc',
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of category must be at least 2 characters', $e->getMessage());
        }

        try {
            $entity = new CategoryEntity(
                name: 'Test',
                description: 'Desc',
                isActive: true,
            );
            $entity->update(
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
            $entity = new CategoryEntity(
                name: 'Test',
                description: 'Desc',
                isActive: true,
            );
            $entity->update(
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
