<?php

namespace App\Http\Controllers;

use App\Http\Cache\CacheCustomerOrder;
use App\Http\Cache\CacheLocation;
use App\Http\Cache\CacheOrder;
use App\Http\Cache\CacheOrderProduct;
use App\Http\Cache\CacheProduct;
use App\Http\Cache\CacheProductCategory;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderProduct;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

// This controller not used
class HomeController extends Controller
{
    public function customerLogin(Request $request)
    {
        $phoneNumber = str_starts_with($request->phoneNumber, '+880') ? substr($request->phoneNumber, 3) : $request->phoneNumber;
        $customer = Customer::firstOrCreate([
            'mobile' => $phoneNumber,
        ]);

        $request->session()->put('customer', $customer->only([
            'name',
            'email',
            'mobile',
            'address',
        ]));

        return back();
    }

    public function home(Request $request)
    {
        if ($location = Location::find($request->location_id)) {
            UseBranch::set($location->branch);

            return redirect()->route('menu');
        }

        $params = [
            'locations' => CacheLocation::get(),
            'steps' => [],
        ];

        return Inertia::render('Home', $params);
    }

    /**
     * Show the Order create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function menu(Request $request)
    {
        if (! UseBranch::id()) {
            return redirect()->route('home');
        }

        if ($request->method() == 'POST') {
            $request->session()->put('new_order', $request->all());

            return redirect()->route('checkout');
        }

        $products = CacheProduct::get();
        $vat_rate = UseBranch::take('vat_rate') ?? 5;
        $order_items = $request->session()->has('new_order') ? $request->session()->get('new_order')['group_items'] : [];

        $categories = CacheProductCategory::get()->map(function ($item) {
            $item->products;

            return $item;
        });

        $params = [
            'location_id' => $request->location_id,
            'locations' => CacheLocation::get(),
            'vat_rate' => $vat_rate,
            'products' => $products,
            'categories' => $categories,
            'order_items' => $order_items,
            'steps' => [],
        ];

        return Inertia::render('Menu', $params);
    }

    public function checkout(Request $request)
    {
        if (! UseBranch::id()) {
            return redirect()->route('home');
        }

        $new_order = $request->session()->get('new_order');
        $customer = $request->session()->get('customer');

        $steps = [
            collect([
                'name' => 'Home',
                'href' => route('home'),
                'status' => 'complete',
            ]),
            collect([
                'name' => 'Cart',
                'href' => route('cart'),
                'status' => 'complete',
            ]),
            collect([
                'name' => 'Checkout',
                'href' => '#',
                'status' => 'current',
            ]),
        ];

        $params = [
            'new_order' => $new_order,
            'customer' => $customer,
            'steps' => $steps,
        ];

        return Inertia::render('Checkout', $params);
    }

    public function checkoutStore(Request $request)
    {
        if (! UseBranch::id()) {
            return redirect()->route('home');
        }

        $customerName = $request->customerName;
        $customerEmail = $request->customerEmail;
        $customerAddress = $request->address;
        $phoneNumber = $request->phoneNumber;
        $new_order = $request->session()->get('new_order');

        DB::beginTransaction();
        try {
            $customer = Customer::updateOrCreate([
                'mobile' => $phoneNumber,
            ], [
                'name' => $customerName,
                'email' => $customerEmail,
                'address' => $customerAddress,
            ]);

            $order = new Order();
            $order->number = Order::withTrashed()->count() + 1;
            $order->invoice_number = now()->format('ymd').$order->number;
            $order->date = now();
            $order->branch_id = UseBranch::id();

            $order->sub_total = $new_order['sub_total'];
            $order->dine_type = $new_order['dine_type'];
            $order->note = $request->note;
            $order->vatable_amount = $new_order['vatable_amount'];
            $order->vat_rate = $new_order['vat_rate'];
            $order->vat_amount = $new_order['vat_amount'];
            $order->total = $new_order['total'];
            $order->customer_id = $customer?->id;
            $order->is_deliverable = true;

            $order->save();

            $request_group_items = is_array($new_order['group_items']) && count($new_order['group_items']) ? $new_order['group_items'] : [];

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
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return back()->with('fail', 'Something went wrong!');
        }
        DB::commit();
        CacheOrder::forget();
        CacheOrderProduct::forget();
        CacheCustomerOrder::forget();

        $request->session()->forget('new_order');
        $request->session()->put('customer', $customer->only([
            'name',
            'email',
            'mobile',
            'address',
        ]));

        return redirect()->route('home')->with('success', 'Your order placed! Our manager will communicate with you soon.');
    }
}
