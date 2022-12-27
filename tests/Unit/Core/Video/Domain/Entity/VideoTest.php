<?php

namespace Tests\Unit\Core\Video\Domain\Entity;

use Core\Video\Domain\Enum\Rating;
use Tests\Unit\TestCase;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\ValueObject\{Image, Media, Enum\Status};
use DateTime;
use Costa\DomainPackage\Domain\Notification\Exception\NotificationException;
use Costa\DomainPackage\ValueObject\Uuid;

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
        $this->assertNull($entity->thumbFile?->path);
        $this->assertNull($entity->thumbHalf?->path);
        $this->assertNull($entity->bannerFile?->path);
        $this->assertNull($entity->trailerFile?->path);
        $this->assertNull($entity->videoFile?->path);
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

    public function testAddCastMembers()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
        );

        $entity->addCastMember('123');
        $entity->addCastMember('456');
        $this->assertCount(2, $entity->castMembers);
    }

    public function testRemoveCastMembers()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            castMembers: ['132', '456']
        );


        $this->assertCount(2, $entity->castMembers);
        $entity->subCastMember('999');
        $this->assertCount(2, $entity->castMembers);
        $entity->subCastMember('456');
        $this->assertCount(1, $entity->castMembers);
        $this->assertEquals("132", $entity->castMembers[0]);
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

    public function testValueObjectThumbFile()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            thumbFile: new Image('test/123.jpg')
        );

        $this->assertEquals('test/123.jpg', $entity->thumbFile->path);
    }

    public function testValueObjectThumbHalf()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            thumbHalf: new Image('test/123.jpg')
        );

        $this->assertEquals('test/123.jpg', $entity->thumbHalf->path);
    }

    public function testValueObjectBannerFile()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            bannerFile: new Image('test/123.jpg')
        );

        $this->assertEquals('test/123.jpg', $entity->bannerFile->path);
    }

    public function testValueObjectTrailerFile()
    {
        $media = new Media(
            path: 'path/123.mp4',
        );

        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            trailerFile: $media
        );

        $this->assertEquals('path/123.mp4', $entity->trailerFile->path);
        $this->assertEquals(2, $entity->trailerFile->status->value);
        $this->assertNull($entity->trailerFile->encoded);
    }

    public function testValueObjectVideoFile()
    {
        $media = new Media(
            path: 'path/123.mp4',
        );

        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            videoFile: $media
        );

        $this->assertEquals('path/123.mp4', $entity->videoFile->path);
        $this->assertEquals(2, $entity->videoFile->status->value);
        $this->assertNull($entity->videoFile->encoded);
    }

    public function testValidation()
    {
        try {
            new Video(
                title: '',
                description: 'de',
                yearLaunched: 2019,
                duration: 20,
                opened: false,
                rating: Rating::L,
            );
        } catch (NotificationException $e) {
            $this->assertEquals('video: The Title is required, The Description minimum is 3', $e->getMessage());
        }

        try {
            new Video(
                title: 'te',
                description: 'de',
                yearLaunched: 2019,
                duration: 20,
                opened: false,
                rating: Rating::L,
            );
        } catch (NotificationException $e) {
            $this->assertEquals('video: The Title minimum is 3, The Description minimum is 3', $e->getMessage());
        }

        try {
            new Video(
                title: str_repeat('a', 256),
                description: 'desc',
                yearLaunched: 2019,
                duration: 20,
                opened: false,
                rating: Rating::L,
            );
        } catch (NotificationException $e) {
            $this->assertEquals('video: The Title maximum is 255', $e->getMessage());
        }

        try {
            new Video(
                title: 'test',
                description: str_repeat('a', 256),
                yearLaunched: 2019,
                duration: 20,
                opened: false,
                rating: Rating::L,
            );
        } catch (NotificationException $e) {
            $this->assertEquals('video: The Description maximum is 255', $e->getMessage());
        }
    }
}
