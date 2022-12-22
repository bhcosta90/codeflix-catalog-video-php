<?php

namespace Core\Video\UseCase\Exceptions;

use Exception;

class GenreNotFound extends Exception
{
    public array $genres;

    public function __construct(string $message, array $genres, int $code = 0)
    {
        $this->genres = $genres;
        parent::__construct($message, $code);
    }
}
