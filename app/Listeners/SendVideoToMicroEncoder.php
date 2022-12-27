<?php

namespace App\Listeners;

use Core\Video\Domain\Event\VideoCreatedEvent;
use Costa\DomainPackage\Domain\Event\EventInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendVideoToMicroEncoder
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(EventInterface $event)
    {
        Log::info($event->getPayload());
    }
}
