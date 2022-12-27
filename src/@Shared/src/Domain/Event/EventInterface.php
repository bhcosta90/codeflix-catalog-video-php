<?php

namespace Costa\DomainPackage\Domain\Event;

interface EventInterface
{
    public function getName(): string;

    public function getPayload(): array;
}
