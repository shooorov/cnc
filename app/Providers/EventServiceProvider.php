<?php

namespace App\Providers;

use App\Events\OrderModified;
use App\Events\OrderPlaced;
use App\Events\ProductionCardModified;
use App\Listeners\AddCustomerToken;
use App\Listeners\BranchSelection;
use App\Listeners\CreateProductionCard;
use App\Listeners\SendOrderForApproval;
use App\Listeners\SendProductionNotification;
use App\Listeners\UpdateCustomerToken;
use App\Listeners\UpdateOrCreateProductionCard;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Login::class => [
            BranchSelection::class,
        ],

        OrderPlaced::class => [
            CreateProductionCard::class,
            SendOrderForApproval::class,
            AddCustomerToken::class,
        ],

        OrderModified::class => [
            UpdateOrCreateProductionCard::class,
            UpdateCustomerToken::class,
        ],

        ProductionCardModified::class => [
            SendProductionNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
