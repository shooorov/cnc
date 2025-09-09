<?php

namespace App\Exports;

use App\Helpers;
use App\Models\Order;
use App\UseBranch;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderExport implements FromView
{
    public function __construct(private $start_date, private $end_date, private $payment_method_id, private $customer_id, private $creator_id)
    {
    }

    public function view(): View
    {
        $last_order = Order::latest()->first();
        $day = $last_order ? now()->parse($last_order->date) : now();

        $end_date = now()->parse($this->end_date ?? (clone $day));
        $start_date = now()->parse($this->start_date ?? (clone $day));

        $customer_id = $this->customer_id;
        $payment_method_id = $this->payment_method_id;
        $creator_id = $this->creator_id;
        $branch_id = UseBranch::id();

        $end_date = $this->end_date ? Helpers::dayEndedAt($this->end_date) : Helpers::dayEndedAt(now());
        $start_date = $this->start_date ? Helpers::dayStartedAt($this->start_date) : Helpers::dayStartedAt(now()->subMonth());

        $orders = Order::when($start_date, function ($query, $start_date) {
            $query->where('date', '>=', $start_date);
        })
            ->when($end_date, function ($query, $end_date) {
                $query->where('date', '<=', $end_date);
            })
            ->when($customer_id, function ($query, $customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->when($creator_id, function ($query, $creator_id) {
                $query->where('creator_id', $creator_id);
            })
            ->whereHas('methods', function ($query) use ($payment_method_id) {
                $query->when($payment_method_id, function ($query2, $payment_method_id) {
                    $query2->where('payment_method_id', $payment_method_id);
                });
            })
            ->when($branch_id, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->orderBy('date', 'DESC')
            ->get();

        return view('exports.orders', [
            'orders' => $orders,
        ]);
    }
}
