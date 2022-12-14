<?php

namespace Core\Genre\Domain\Entity;

use Costa\DomainPackage\Domain\Entity\Trait\EntityTrait;
use Costa\DomainPackage\Domain\Entity\Trait\MethodsMagicsTrait;
use Costa\DomainPackage\Domain\Validation\DomainValidation;
use Costa\DomainPackage\ValueObject\Uuid;
use DateTime;

class Genre
{
    use MethodsMagicsTrait, EntityTrait;

    public function __construct(
        protected string $name,
        protected bool $isActive = true,
        protected ?Uuid $id = null,
        protected ?DateTime $createdAt = null,
        protected array $categories = [],
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

    public function addCategory(string $category)
    {
        array_push($this->categories, $category);
    }

    public function subCategory(string $category)
    {
        $this->categories = array_diff($this->categories, [$category]);
    }

    private function validate()
    {
        DomainValidation::notNull($this->name);
        DomainValidation::strMinLength($this->name, 3, 'Name of genre must be at least 2 characters');
        DomainValidation::strMaxLength($this->name, 255, 'Name of genre must be less than 255 characters');
    }
}
