<?php

namespace App\Http\Controllers\Api;

use App\Http\Cache\CacheCustomerOrder;
use App\Http\Cache\CacheOrder;
use App\Http\Cache\CacheOrderProduct;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $customerAddress = $request->address;
        $phoneNumber = $request->phone_number;

        DB::beginTransaction();
        try {
            $customer = Customer::firstOrCreate([
                'mobile' => $phoneNumber,
            ]);

            $order = new Order();
            $order->number = Order::withTrashed()->count() + 1;
            $order->invoice_number = now()->format('ymd').$order->number;
            $order->date = now();
            $order->branch_id = $request->branch_id;

            $order->sub_total = $request->sub_total;
            $order->dine_type = $request->dine_type;
            $order->note = $request->note;
            $order->vatable_amount = $request->vatable_amount;
            $order->vat_rate = $request->vat_rate;
            $order->vat_amount = $request->vat_amount;
            $order->delivery_cost = $request->delivery_cost;
            $order->total = $request->total;
            $order->customer_id = $customer?->id;
            $order->is_deliverable = true;

            $order->save();

            $request_group_items = is_array($request->group_items) && count($request->group_items) ? $request->group_items : [];

            foreach ($request_group_items as $group_item) {
                if ($group_item['quantity'] > 0) {
                    OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $group_item['product_id'],
                        'rate' => $group_item['rate'],
                        'quantity' => $group_item['quantity'],
                        'total' => $group_item['quantity'] * $group_item['rate'],
                    ]);
                }
            }

            OrderDelivery::create([
                'status' => 'pending',
                'address' => $customerAddress,
                'order_id' => $order->id,
                'customer_id' => $customer->id,
            ]);

            if (! $customer->addresses()->where('address', $customerAddress)->first()) {
                $record = new Address(['address' => $customerAddress]);
                $customer->addresses()->save($record);
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return response()->json([
                'show' => true,
                'success' => false,
                'message' => 'Something went wrong!',
            ], 500);
        }
        DB::commit();
        CacheOrder::forget();
        CacheOrderProduct::forget();
        CacheCustomerOrder::forget();

        return response()->json([
            'number' => $order->invoice_number,
            'show' => true,
            'success' => true,
            'message' => 'Your order placed! Our manager will communicate with you soon.',
        ], 201);
    }

    public function show(Request $request)
    {
        $customer = Customer::where('mobile', $request->phone_number)->first();

        if (! $customer) {
            return response()->json([
                'show' => true,
                'success' => false,
                'message' => 'Customer not found! Please try again.',
            ]);
        }

        $order = Order::where('customer_id', $customer->id)->where('number', $request->number)->first();
        $order?->products;

        if (! $order) {
            return response()->json([
                'show' => true,
                'success' => false,
                'message' => 'Order not found!',
            ], 404);
        }

        return response()->json([
            'order' => $order,
        ], 200);
    }
}
