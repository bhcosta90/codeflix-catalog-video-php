<?php

namespace Shared\Domain\Validation;

use Shared\Domain\Entity\Entity;

interface ValidatorInterface
{
    public function validate(Entity $entity);
}
