<?php

namespace Core\Category\Domain\Entity;

use Core\Shared\Domain\Entity\Exception\EntityValidationException;
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
        $this->validate();
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
        $this->validate();
    }

    private function validate()
    {
        if (strlen($this->name) <= 2) {
            throw new EntityValidationException('Name of category must be at least 2 characters');
        }

        if (strlen($this->name) > 255) {
            throw new EntityValidationException('Name of category must be less than 255 characters');
        }

        if (!empty($this->description) && strlen($this->description) <= 2) {
            throw new EntityValidationException('Description of category must be at least 2 characters');
        }

        if (strlen($this->description) > 255) {
            throw new EntityValidationException('Description of category must be less than 255 characters');
        }
    }
}
