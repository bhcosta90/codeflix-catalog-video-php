<?php

namespace Core\Category\Domain\Entity;

use Core\Shared\Domain\Entity\Trait\{EntityTrait, MethodsMagicsTrait};
use Core\Shared\Domain\Validation\DomainValidation;
use Core\Shared\ValueObject\Uuid;
use DateTime;

class CategoryEntity
{
    use MethodsMagicsTrait, EntityTrait;

    public function __construct(
        protected string $name,
        protected ?string $description = null,
        protected bool $isActive = true,
        protected Uuid|string $id = '',
        protected DateTime|string $createdAt = '',
    ) {
        $this->id = $this->id ? new Uuid($this->id) : Uuid::random();
        $this->createdAt = new DateTime($this->createdAt);
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
        DomainValidation::notNull($this->name);
        DomainValidation::strMinLength($this->name, 3, 'Name of category must be at least 2 characters');
        DomainValidation::strMaxLength($this->name, 255, 'Name of category must be less than 255 characters');
        DomainValidation::strCanNullAndMaxLength($this->description, 255, 'Description of category must be less than 255 characters');
    }
}
