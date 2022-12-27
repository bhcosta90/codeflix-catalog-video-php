<?php

namespace Core\Video\Domain\Factory;

use Core\Video\Domain\Validation\VideoRakitValidator;
use Costa\DomainPackage\Domain\Validation\ValidatorInterface;

class VideoValidator
{
    public static function create(): ValidatorInterface
    {
        return new VideoRakitValidator();
    }
}
