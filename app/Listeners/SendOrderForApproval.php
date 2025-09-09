<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderApproval as MailOrderApproval;
use App\Models\Order;
use App\Models\OrderApproval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendOrderForApproval implements ShouldQueue
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
        $order = Order::find($event->order['id']);

        $branch = $order->branch;
        $customer = $order->customer;
        $user = $customer?->user;

        if (! $user || ! ($order->created_at->eq($order->updated_at))) {
            return;
        }

        $o_approval = OrderApproval::create([
            'token' => Str::random(64),
            'status' => 'pending',
            'order_id' => $order->id,
            'user_id' => $user->id,
        ]);

        $params = [
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
            'order_id' => $order->id,
            'order_amount' => $order->total,
            'date' => now(),
            'token' => $o_approval->token,
        ];

        Mail::to([$user->email])
            ->bcc([])
            ->queue(new MailOrderApproval($params));
    }
}
