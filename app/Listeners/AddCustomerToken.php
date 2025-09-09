<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Http\Cache\CacheCustomerToken;
use App\Models\CustomerToken;
use App\Models\Message;
use App\Models\Order;
use App\ShortMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AddCustomerToken implements ShouldQueue
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
    public function handle(OrderPlaced $event)
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
        foreach ($order->products as $item) {
            $category = $item->product?->product_category;
            $token_value = $category?->token_amount[$branch_id] ?? 0;
            $total_token_amount += ($item->total * ($token_value / 100));
        }

        if ($order->status == 'complete') {
            CustomerToken::create([
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'token' => $total_token_amount,
            ]);

            $customer->tokenUpdate();
            CacheCustomerToken::forget();

            $this->sendMessage($customer);
        }

    }

    public function sendMessage($customer)
    {
        do {
            $uniqueId = uniqid();
        } while (Message::where('unique_id', $uniqueId)->first());

        $content = "Your current available token is {$customer->balance}";

        $message = Message::updateOrCreate([
            'customer_id' => $customer->id,
            'is_sent' => false,
        ], [
            'contact' => $customer->mobile,
            'content' => $content,
        ]);

        $response = ShortMessage::single($message->contact, $message->content, $uniqueId)->send();

        if ($response['status'] == 'FAILED') {
            Log::error('SMS sending failed! '.json_encode($response));
        }

        $message->is_sent = ($response['status'] == 'SUCCESS');
        $message->response = json_encode($response);

        $message->save();
    }
}
