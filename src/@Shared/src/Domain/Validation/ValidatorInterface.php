<?php

namespace Costa\DomainPackage\Domain\Validation;

use Shared\Domain\Entity\Entity;

interface ValidatorInterface
{
    public function validate(Entity $entity);
}
