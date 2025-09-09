<?php

namespace App\Listeners;

use App\Events\OrderModified;
use App\Http\Cache\CacheCustomerToken;
use App\Models\CustomerToken;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCustomerToken implements ShouldQueue
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

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(OrderModified $event)
    {
        if (! config('module.token')) {
            return;
        }

        $order = Order::find($event->order['id']);
        $branch_id = $order->branch_id;

        $customer = $order->customer;
        if (! $customer) {
            return;
        }

        $total_token_amount = 0;
        $previous_token = $customer->token;

        if ($order->status == 'complete') {
            foreach ($order->products as $item) {
                $category = $item->product?->product_category;
                $token_value = $category?->token_amount[$branch_id] ?? 0;
                $total_token_amount += ($item->total * ($token_value / 100));
            }

            CustomerToken::updateOrCreate([
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
            ], [
                'token' => $total_token_amount,
            ]);

            $customer->tokenUpdate();
            CacheCustomerToken::forget();

            if ($customer->token != $previous_token) {
                (new AddCustomerToken())->sendMessage($customer);
            }
        }
    }
}
