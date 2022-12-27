<?php

namespace Core\Video\Domain\ValueObject;

use Costa\DomainPackage\Domain\Entity\Trait\MethodsMagicsTrait;

class Image
{
    use MethodsMagicsTrait;

    public function __construct(protected string $path)
    {
        //
    }
}
