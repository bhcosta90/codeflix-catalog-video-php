<?php

namespace Core\Video\UseCase\Exceptions;

use Exception;

class CastMemberNotFound extends Exception
{
    public array $castMembers;

    public function __construct(string $message, array $castMembers, int $code = 0)
    {
        $this->castMembers = $castMembers;
        parent::__construct($message, $code);
    }
}
