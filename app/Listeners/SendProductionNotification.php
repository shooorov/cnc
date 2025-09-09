<?php

namespace App\Listeners;

use App\Events\ProductionCardModified;

class SendProductionNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductionCardModified $event): void
    {
        //
    }
}
