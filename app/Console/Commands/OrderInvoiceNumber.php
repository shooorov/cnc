<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class OrderInvoiceNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:invoice-number';

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
        Order::withTrashed()->chunk(200, function (Collection $orders) {
            foreach ($orders as $order) {
                $order->invoice_number = now()->parse($order->date)->format('ymd').$order->number;
                $order->table_name = $order->stuff?->name;
                $order->save();

                echo $order->id."\n";
            }
        });

    }
}
