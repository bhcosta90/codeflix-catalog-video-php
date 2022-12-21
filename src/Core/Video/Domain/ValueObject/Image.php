<?php

namespace Core\Video\Domain\ValueObject;

use Shared\Domain\Entity\Trait\MethodsMagicsTrait;

class Image
{
    use MethodsMagicsTrait;

    public function __construct(protected string $path)
    {
        //
    }
}
