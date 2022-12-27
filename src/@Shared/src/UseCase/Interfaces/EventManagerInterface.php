<?php

namespace Costa\DomainPackage\UseCase\Interfaces;

interface EventManagerInterface
{
    public function dispatch(object $data): bool;
}
