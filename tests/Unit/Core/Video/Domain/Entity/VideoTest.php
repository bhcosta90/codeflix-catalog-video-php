<?php

namespace Tests\Unit\Core\Video\Domain\Entity;

use Core\Video\Domain\Enum\Rating;
use Tests\Unit\TestCase;
use Core\Video\Domain\Entity\Video;
use DateTime;
use Shared\ValueObject\Uuid;

class VideoTest extends TestCase
{
    public function testAttributes()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
        );
        $this->assertEquals('Test', $entity->title);
        $this->assertEquals('Description', $entity->description);
        $this->assertEquals(2019, $entity->yearLaunched);
        $this->assertEquals(20, $entity->duration);
        $this->assertFalse($entity->opened);
        $this->assertEquals('L', $entity->rating->value);
        $this->assertFalse($entity->publish);
        $this->assertNotEmpty($entity->id());
        $this->assertNotEmpty($entity->createdAt());
    }

    public function testAddCategories()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
        );

        $entity->addCategory('123');
        $entity->addCategory('456');
        $this->assertCount(2, $entity->categories);
    }

    public function testRemoveCategories()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            categories: ['132', '456']
        );


        $this->assertCount(2, $entity->categories);
        $entity->subCategory('999');
        $this->assertCount(2, $entity->categories);
        $entity->subCategory('456');
        $this->assertCount(1, $entity->categories);
        $this->assertEquals("132", $entity->categories[0]);
    }

    public function testAddGenres()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
        );

        $entity->addGenre('123');
        $entity->addGenre('456');
        $this->assertCount(2, $entity->genres);
    }

    public function testRemoveGenres()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            genres: ['132', '456']
        );


        $this->assertCount(2, $entity->genres);
        $entity->subGenre('999');
        $this->assertCount(2, $entity->genres);
        $entity->subGenre('456');
        $this->assertCount(1, $entity->genres);
        $this->assertEquals("132", $entity->genres[0]);
    }

    public function testEnabled()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            isActive: false
        );

        $this->assertFalse($entity->isActive);
        $entity->enabled();
        $this->assertTrue($entity->isActive);
    }

    public function testDisabled()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
        );

        $this->assertTrue($entity->isActive);
        $entity->disabled();
        $this->assertFalse($entity->isActive);
    }

    public function testUpdate()
    {
        $entity = new Video(
            id: new Uuid('421fb927-d050-47af-813f-a554b6b7d9cb'),
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            createdAt: new DateTime($date = '2020-01-01 00:00:00')
        );

        $entity->update(
            title: 'update',
            description: 'update 2',
            yearLaunched: 2000,
            duration: 50,
            opened: true,
            rating: Rating::RATE14
        );

        $this->assertEquals('update', $entity->title);
        $this->assertEquals('update 2', $entity->description);
        $this->assertEquals(2000, $entity->yearLaunched);
        $this->assertEquals(50, $entity->duration);
        $this->assertTrue($entity->opened);
        $this->assertEquals('14', $entity->rating->value);
        $this->assertEquals($date, $entity->createdAt());
    }
}
