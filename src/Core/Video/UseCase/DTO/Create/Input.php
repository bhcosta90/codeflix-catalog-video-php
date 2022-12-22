<?php

namespace Core\Video\UseCase\DTO\Create;

use Core\Video\Domain\Enum\Rating;

class Input
{
    public function __construct(
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public int $opened,
        public Rating $rating,
        public array $categories = [],
        public array $genres = [],
        public array $castMembers = [],
        public array $thumbFile = [],
        public array $thumbHalf = [],
        public array $bannerFile = [],
        public array $trailerFile = [],
        public array $videoFile = [],
    ) {
        //
    }
}
