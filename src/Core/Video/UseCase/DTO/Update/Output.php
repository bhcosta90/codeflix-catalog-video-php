<?php

namespace Core\Video\UseCase\DTO\Update;

class Output
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public int $opened,
        public string $rating,
        public ?string $created_at,
        public array $categories = [],
        public array $genres = [],
        public array $castMembers = [],
        public ?string $thumbFile = null,
        public ?string $thumbHalf = null,
        public ?string $bannerFile = null,
        public ?string $trailerFile = null,
        public ?string $videoFile = null,
    ) {
        //
    }
}
