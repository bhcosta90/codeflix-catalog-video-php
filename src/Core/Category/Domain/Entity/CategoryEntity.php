<?php

namespace Core\Category\Domain\Entity;

use Core\Shared\Domain\Entity\Trait\MethodsMagicsTrait;

class CategoryEntity
{
    use MethodsMagicsTrait;

    public function __construct(
        protected string $name,
        protected ?string $description = null,
        protected bool $isActive = true,
        protected string $id = '',
    ) {
        //
    }

    public function enabled(): void
    {
        $this->isActive = true;
    }

    public function disabled(): void
    {
        $this->isActive = false;
    }

    public function update(
        string $name,
        ?string $description
    ) {
        $this->name = $name;
        $this->description = $description;
    }
}
