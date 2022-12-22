<?php

namespace Shared\UseCase\Interfaces;

interface EventManagerInterface
{
    public function dispatch(object $data): bool;
}
