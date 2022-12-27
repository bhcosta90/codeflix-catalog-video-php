<?php

namespace Costa\DomainPackage\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    public function __construct(protected ?string $id = null)
    {
        $this->ensureIsValid($id);
    }

    public static function random(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    private function ensureIsValid(string $value)
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $value));
        }
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
