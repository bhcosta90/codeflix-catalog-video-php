<?php

namespace Core\Video\UseCase\DTO\Create;

class Input
{
    public function __construct(
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public int $opened,
        public int|string $rating,
        public array $categories = [],
        public array $genres = [],
        public array $castMembers = [],
        public ?array $thumbFile = [],
        public ?array $thumbHalf = [],
        public ?array $bannerFile = [],
        public ?array $trailerFile = [],
        public ?array $videoFile = [],
    ) {
        //
    }
}
