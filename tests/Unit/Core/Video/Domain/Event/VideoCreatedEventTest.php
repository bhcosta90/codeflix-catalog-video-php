<?php

namespace Tests\Unit\Core\Video\Domain\Event;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Event\VideoCreatedEvent;
use Core\Video\Domain\ValueObject\Media;
use Mockery;
use Shared\ValueObject\Uuid;
use Tests\Unit\TestCase;

class VideoCreatedEventTest extends TestCase
{
    public function testGetName()
    {
        $entity = new Video(
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
        );

        $event = new VideoCreatedEvent($entity);
        $this->assertEquals('video.created', $event->getName());
    }

    public function testGetPayloadEmpty()
    {
        $entity = new Video(
            id: new Uuid('563bef80-4467-4455-9a02-506f5e52c99f'),
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
        );

        $event = new VideoCreatedEvent($entity);
        $this->assertEquals([
            'resource_id' => '563bef80-4467-4455-9a02-506f5e52c99f',
            'trailer_file' => null,
            'video_file' => null,
        ], $event->getPayload());
    }

    public function testGetPayloadTrailer()
    {
        $entity = new Video(
            id: new Uuid('563bef80-4467-4455-9a02-506f5e52c99f'),
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            trailerFile: new Media('path/test.mp4')
        );

        $event = new VideoCreatedEvent($entity);
        $this->assertEquals([
            'resource_id' => '563bef80-4467-4455-9a02-506f5e52c99f',
            'trailer_file' => 'path/test.mp4',
            'video_file' => null,
        ], $event->getPayload());
    }

    public function testGetPayloadVideo()
    {
        $entity = new Video(
            id: new Uuid('563bef80-4467-4455-9a02-506f5e52c99f'),
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            videoFile: new Media('path/test.mp4')
        );

        $event = new VideoCreatedEvent($entity);
        $this->assertEquals([
            'resource_id' => '563bef80-4467-4455-9a02-506f5e52c99f',
            'trailer_file' => null,
            'video_file' => 'path/test.mp4',
        ], $event->getPayload());
    }

    public function testGetPayloadTrailerAndVideo()
    {
        $entity = new Video(
            id: new Uuid('563bef80-4467-4455-9a02-506f5e52c99f'),
            title: 'Test',
            description: 'Description',
            yearLaunched: 2019,
            duration: 20,
            opened: false,
            rating: Rating::L,
            trailerFile: new Media('trailer/test.mp4'),
            videoFile: new Media('video/test.mp4'),
        );

        $event = new VideoCreatedEvent($entity);
        $this->assertEquals([
            'resource_id' => '563bef80-4467-4455-9a02-506f5e52c99f',
            'trailer_file' => 'trailer/test.mp4',
            'video_file' => 'video/test.mp4',
        ], $event->getPayload());
    }
}
