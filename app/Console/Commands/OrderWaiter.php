<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class OrderWaiter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:order-waiter';

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
                $user = User::where('email', $order->employee?->email)->first();
                $employee = $order->employee;

                if ($user) {
                    $order->waiter_id = $user->id;
                    $order->save();
                } elseif ($employee) {
                    echo 'waiter found order: ';
                } else {
                    echo 'waiter missing order: ';
                }

                echo $order->id."\n";
            }
        });

    }
}
