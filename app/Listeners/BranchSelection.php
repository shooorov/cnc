<?php

namespace App\Listeners;

use App\UseBranch;
use Illuminate\Auth\Events\Login;

class BranchSelection
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
    public function handle(Login $event): void
    {
        $branches = UseBranch::available();

        if ($branches->count() == 1) {
            UseBranch::set($branches->first());
        }
    }
}
