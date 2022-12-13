<?php

namespace Core\Shared\UseCase\Exception;

use Exception;

class NotFoundException extends Exception
{
    public function __construct($id)
    {
        parent::__construct("ID {$id} not found.");
    }
}
