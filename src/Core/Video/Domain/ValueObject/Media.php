<?php

namespace Core\Video\Domain\ValueObject;

use Costa\DomainPackage\Domain\Entity\Trait\MethodsMagicsTrait;

class Media
{
    use MethodsMagicsTrait;

    public function __construct(
        protected string $path,
        protected Enum\Status $status = Enum\Status::PENDING,
        protected ?string $encoded = null,
    ) {
        //
    }
}
