<?php

namespace Shared\Domain\Event;

interface EventInterface
{
    public function getName(): string;

    public function getPayload(): array;
}
