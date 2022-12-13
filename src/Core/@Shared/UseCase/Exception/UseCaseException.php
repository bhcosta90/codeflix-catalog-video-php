<?php

namespace Core\Shared\UseCase\Exception;

use Exception;

class UseCaseException extends Exception
{
    public function __construct($class)
    {
        parent::__construct('The class ' . $class . ' is wrong.');
    }
}
