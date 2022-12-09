<?php

namespace Tests\Unit\Category\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Core\Category\Domain\Entity\CategoryEntity;

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

    public function testUpdated(){
        $id = 'fake.id';

        $category = new CategoryEntity(
            name: 'Test',
            description: 'Desc',
            isActive: true,
            id: $id,
        );

        $category->update(
            name: 'Test 2',
            description: 'Desc 2',
        );

        $this->assertEquals('Test 2', $category->name);
        $this->assertEquals('Desc 2', $category->description);

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
}
