<?php

namespace Core\Video\UseCase\Exceptions;

use Exception;

class CategoryNotFound extends Exception
{
    public array $categories;

    public function __construct(string $message, array $categories, int $code = 0)
    {
        $this->categories = $categories;
        parent::__construct($message, $code);
    }
}
