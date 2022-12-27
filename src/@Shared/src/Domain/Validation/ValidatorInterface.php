<?php

namespace Costa\DomainPackage\Domain\Validation;

use Costa\DomainPackage\Domain\Entity\Entity;

interface ValidatorInterface
{
    public function validate(Entity $entity);
}
