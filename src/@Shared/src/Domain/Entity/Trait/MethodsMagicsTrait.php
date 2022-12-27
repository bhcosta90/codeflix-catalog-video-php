<?php

namespace Costa\DomainPackage\Domain\Entity\Trait;

use Exception;

trait MethodsMagicsTrait
{
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        $className = get_class($this);
        throw new Exception("Property {$property} not found in class {$className}");
    }
}
