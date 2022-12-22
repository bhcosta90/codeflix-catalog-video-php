<?php

namespace Core\Video\Domain\Event;

use Core\Video\Domain\Entity\Video;
use Shared\Domain\Event\EventInterface;

class VideoCreatedEvent implements EventInterface
{
    public function __construct(protected Video $video)
    {
        //
    }

    public function getName(): string
    {
        return 'video.created';
    }

    public function getPayload(): array
    {
        return [
            'resource_id' => $this->video->id(),
            'trailer_file' => $this->video->trailerFile?->path,
            'video_file' => $this->video->videoFile?->path,
        ];
    }
}
