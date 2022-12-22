<?php

namespace Core\Video\Domain\Factory;

use Core\Video\Domain\Validation\VideoRakitValidator;
use Shared\Domain\Validation\ValidatorInterface;

class VideoValidator
{
    public static function create(): ValidatorInterface
    {
        return new VideoRakitValidator();
    }
}
