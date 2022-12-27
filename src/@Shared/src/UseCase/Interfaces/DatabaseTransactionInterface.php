<?php

namespace Costa\DomainPackage\UseCase\Interfaces;

interface DatabaseTransactionInterface
{
    public function commit();

    public function rollback();
}
