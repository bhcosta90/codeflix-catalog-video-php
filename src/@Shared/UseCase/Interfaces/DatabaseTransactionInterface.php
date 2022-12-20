<?php

namespace Shared\UseCase\Interfaces;

interface DatabaseTransactionInterface
{
    public function commit();

    public function rollback();
}
