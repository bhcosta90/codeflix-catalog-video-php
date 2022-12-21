<?php

namespace Tests\Unit\Core\CastMember\Domain\Entity;

use Tests\Unit\TestCase;
use Core\CastMember\Domain\Entity\CastMemberEntity;
use Core\CastMember\Domain\Enum\Type;
use DateTime;
use Shared\Domain\Entity\Exception\EntityValidationException;
use Shared\ValueObject\Uuid;
use Throwable;

class CastMemberEntityTest extends TestCase
{
    public function testAttributes()
    {
        $entity = new CastMemberEntity(
            name: 'Test',
            isActive: true,
            type: Type::ACTOR,
        );

        $this->assertEquals('Test', $entity->name);
        $this->assertEquals(2, $entity->type->value);
        $this->assertTrue($entity->isActive);
        $this->assertNotEmpty($entity->id());
        $this->assertNotEmpty($entity->createdAt());
    }

    public function testEnabled()
    {
        $category = new CastMemberEntity(
            name: 'Test',
            type: Type::ACTOR,
            isActive: false,
        );

        $this->assertFalse($category->isActive);
        $category->enabled();
        $this->assertTrue($category->isActive);
    }

    public function testDisabled()
    {
        $entity = new CastMemberEntity(
            name: 'Test',
            type: Type::ACTOR,
        );

        $this->assertTrue($entity->isActive);
        $entity->disabled();
        $this->assertFalse($entity->isActive);
    }

    public function testUpdated()
    {
        $id = new Uuid('b257aaca-75f2-4cdf-a96f-d438e1e891cc');

        $entity = new CastMemberEntity(
            name: 'Test',
            type: Type::ACTOR,
            isActive: true,
            id: $id,
            createdAt: new DateTime($date = '2020-01-01 00:00:00')
        );

        $entity->update(
            name: 'Test 2',
            type: Type::DIRECTOR,
        );

        $this->assertEquals('Test 2', $entity->name);
        $this->assertEquals(1, $entity->type->value);
        $this->assertEquals($id, $entity->id());
        $this->assertEquals($date, $entity->createdAt());
    }

    public function testExceptionName()
    {
        try {
            new CastMemberEntity(
                name: 'Te',
                type: Type::ACTOR,
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of cast member must be at least 2 characters', $e->getMessage());
        }

        try {
            new CastMemberEntity(
                name: str_repeat('Te', 256),
                type: Type::ACTOR,
                isActive: true,
            );
            $this->assertTrue(false);
        } catch (Throwable $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals('Name of cast member must be less than 255 characters', $e->getMessage());
        }
    }
}
