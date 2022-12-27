<?php

namespace Core\Video\Domain\ValueObject\Enum;

enum Status: int
{
    case PROCESSING = 0;
    case COMPLETED = 1;
    case PENDING = 2;
}
