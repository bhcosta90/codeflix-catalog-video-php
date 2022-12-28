<?php

namespace Core\Video\UseCase\DTO\Update;

class Output
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public int $year_launched,
        public int $duration,
        public int $opened,
        public string $rating,
        public ?string $created_at,
        public array $categories = [],
        public array $genres = [],
        public array $cast_members = [],
        public ?string $thumb_file = null,
        public ?string $thumb_half = null,
        public ?string $banner_file = null,
        public ?string $trailer_file = null,
        public ?string $video_file = null,
    ) {
        //
    }
}
