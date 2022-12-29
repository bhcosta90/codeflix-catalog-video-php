<?php

namespace Core\Video\UseCase\DTO\ChangeEncodedPath;

class Input
{
    public function __construct(
        public string $id,
        public ?string $pathTrailer = null,
        public ?string $pathVideo = null,
    ) {
        //
    }
}
