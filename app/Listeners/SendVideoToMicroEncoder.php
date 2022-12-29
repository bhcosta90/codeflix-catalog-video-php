<?php

namespace App\Listeners;

use Costa\DomainPackage\Domain\Event\EventInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendVideoToMicroEncoder implements ShouldQueue
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
