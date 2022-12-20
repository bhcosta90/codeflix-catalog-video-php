<?php

namespace Core\Genre\Domain\Entity;

use Shared\Domain\Entity\Trait\{EntityTrait, MethodsMagicsTrait};
use Shared\Domain\Validation\DomainValidation;
use Shared\ValueObject\Uuid;
use DateTime;

class GenreEntity
{
    use MethodsMagicsTrait, EntityTrait;

    public function __construct(
        protected string $name,
        protected bool $isActive = true,
        protected ?Uuid $id = null,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();
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
    ) {
        $this->name = $name;
        $this->validate();
    }

    private function validate()
    {
        DomainValidation::notNull($this->name);
        DomainValidation::strMinLength($this->name, 3, 'Name of genre must be at least 2 characters');
        DomainValidation::strMaxLength($this->name, 255, 'Name of genre must be less than 255 characters');
    }
}
