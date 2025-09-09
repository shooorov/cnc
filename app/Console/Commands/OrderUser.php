<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class OrderUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:order-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        OrderHistory::truncate();
        Order::withTrashed()->chunk(200, function (Collection $orders) {
            foreach ($orders as $order) {
                $order->manager_id = $order->creator_id;
                $order->save();

                foreach ($order->history ?? [] as $history) {
                    OrderHistory::create([
                        'status' => $order->status,
                        'sub_total' => $history['sub_total'] ?? 0,
                        'total' => $history['total'] ?? 0,
                        'discount_amount' => $history['discount_amount'] ?? 0,
                        'vat_amount' => $history['vat_amount'] ?? 0,
                        'user_id' => $history['user_id'],
                        'order_id' => $order->id,
                        'created_at' => now()->parse($history['date']),
                    ]);
                }

                echo $order->id."\n";
            }
        });

    }
}
