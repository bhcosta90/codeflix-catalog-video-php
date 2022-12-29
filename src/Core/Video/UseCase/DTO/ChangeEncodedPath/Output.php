<?php

namespace Core\Video\UseCase\DTO\ChangeEncodedPath;

class Output
{
    public function __construct(
        public string $id,
        public ?string $pathTrailer = null,
        public ?string $pathVideo = null,
        public bool $success = true,
        public int $quantity = 0,
    ) {
        //
    }
}
