<?php

namespace App\Services;

use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;

class VideoEventManager implements VideoEventManagerInterface
{
    public function dispatch(object $event): void
    {
        event($event);
    }
}
