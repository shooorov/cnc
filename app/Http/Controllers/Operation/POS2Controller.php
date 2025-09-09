<?php

namespace App\Http\Controllers\Operation;

use App\Data;
use App\Events\OrderModified;
use App\Events\OrderPlaced;
use App\Helpers;
use App\Http\Cache\CacheBranchAccess;
use App\Http\Cache\CacheCustomer;
use App\Http\Cache\CacheCustomerOrder;
use App\Http\Cache\CacheOrder;
use App\Http\Cache\CacheOrderProduct;
use App\Http\Cache\CacheOrderProductQuantity;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderPaymentMethod;
use App\Models\OrderProduct;
use App\Models\Stuff;
use App\Models\User;
use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class POS2Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the Order create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request, Order $order)
    {
        // $order = Order::find($request->order_id);

        $end_date = Helpers::dayEndedAt();
        $start_date = Helpers::dayStartedAt();

        $vat_rate = UseBranch::take('vat_rate') ?? 5;

        $pending_orders = Data::whereBranch(UseBranch::id())->whereBetween($start_date, $end_date)->pendingOrders();

        $branch_id = UseBranch::id();
        $tables = Stuff::all();

        $branch_users = CacheBranchAccess::get()->filter(fn ($item) => $item->branch_id == $branch_id && $item->is_checked)->pluck('user_id');
        $waiters = User::whereIsWaiter(true)->whereIn('id', $branch_users->toArray())->get();

        $params = [
            'vat_rate' => $vat_rate,
            'tables' => $tables,
            'waiters' => $waiters,
            'pending_orders' => $pending_orders,
            'order' => $order,
        ];

        return Inertia::render('Operation/POS2', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @return Response
     */
    public function store(OrderRequest $request)
    {
        DB::beginTransaction();
        $update = false;

        if ($request->order_id) {
            $order = Order::findOrFail($request->order_id);
            $update = true;
        } else {
            $order = new Order;

            $order->number = Order::withTrashed()->count() + 1;
            $order->invoice_number = now()->format('ymd').$order->number;
            $order->branch_id = UseBranch::id();
            $order->creator_id = $request->user()->id;
        }

        $customer = null;
        if ($request->customer_mobile) {
            $customer = Customer::updateOrCreate(
                ['mobile' => $request->customer_mobile],
                ['name' => $request->customer_name ?? '']
            );
            CacheCustomer::forget();
        }
        // This values store modifying history
        $current_status = $order->status;
        $current_total = $order->total;
        $modified_total = $request->total;

        $messages = [];

        $order->date = $request->date_time ? now()->parse($request->date_time) : now();
        $order->sub_total = $request->sub_total;
        $order->dine_type = $request->dine_type;
        $order->guest_number = $request->guest_number ?? 1;
        $order->note = $request->note;
        $order->stuff_id = $request->table;
        $order->discount_type = $request->discount_type;
        $order->discount_rate = $request->discount_rate;
        $order->discount_amount = $request->discount_amount;
        $order->service_cost = $request->service_cost;
        $order->vatable_amount = $request->vatable_amount;
        $order->vat_rate = $request->vat_rate;
        $order->vat_amount = $request->vat_amount;
        $order->total = $request->total;
        $order->token = $request->token ?? 0;
        $order->cash = $request->cash;
        $order->change = $request->change;
        $order->customer_id = $customer?->id;
        $order->waiter_id = $request->waiter_id;

        // If paid by cash or other payment method
        if ($request->cash >= $request->total) {
            $order->status = 'complete';
            $order->manager_id = $request->user()->id;
        } elseif (! $order->order_delivery) {
            $order->status = 'accept';
        }

        if (RolePermission::isEnabled('POS.update_completed_order') || $current_status != 'complete') {

            if ($current_status == 'complete' && $current_total != $modified_total) {
                OrderHistory::create([
                    'status' => $order->status,
                    'sub_total' => $order->sub_total,
                    'total' => $order->total,
                    'discount_amount' => $order->discount_amount,
                    'vat_amount' => $order->vat_amount,
                    'user_id' => $request->user()->id,
                    'order_id' => $order->id,
                ]);
            }

            $messages[] = 'Order modified successfully';

            $order->save();
        }

        $order_delivery = $order->order_delivery;
        if ($order_delivery) {
            $order_delivery->rider_id = $request->rider_id;
            $order_delivery->save();
            if ($order->status != $request->status) {
                $order->status = $request->status;
            }

            $messages[] = 'Order delivery rider modified successfully';
        }

        if ($current_status != 'complete' || RolePermission::isEnabled('POS.update_completed_order_payment_methods')) {

            $group_methods = is_array($request->group_methods) && count($request->group_methods) ? $request->group_methods : [];

            OrderPaymentMethod::where('order_id', $order->id)->delete();

            foreach ($group_methods as $group_method) {
                if (isset($group_method['amount']) && $group_method['amount'] > 0) {
                    OrderPaymentMethod::create([
                        'order_id' => $order->id,
                        'payment_method_id' => $group_method['payment_method_id'],
                        'amount' => $group_method['amount'],
                    ]);
                }
            }

            $messages[] = 'Order payment method modified successfully';
        }

        if (RolePermission::isEnabled('POS.update_completed_order_foods') || $current_status != 'complete') {

            $previous_items = $order->products->pluck('id')->toArray();
            $group_items = is_array($request->group_items) && count($request->group_items) ? $request->group_items : [];

            foreach ($group_items as $item) {
                $order_product_id = $item['id'];
                $product_id = $item['product_id'];
                $quantity = intval($item['quantity']);
                $rate = floatval($item['rate']);
                $total = $quantity * $rate;

                if ($quantity == 0) {
                    continue;
                }

                if (($key = array_search($order_product_id, $previous_items)) !== false) {
                    unset($previous_items[$key]);
                }

                OrderProduct::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $product_id,
                    ],
                    [
                        'rate' => $rate,
                        'quantity' => $quantity,
                        'total' => $total,
                    ]
                );
            }

            OrderProduct::where('order_id', $order->id)->whereIn('id', $previous_items)->delete();

            $messages[] = 'Order items modified successfully';
        }

        CacheOrder::forget();
        CacheOrderProduct::forget();
        CacheOrderProductQuantity::forget();
        CacheCustomerOrder::forget();
        CacheCustomer::forget();

        DB::commit();

        if ($update) {
            OrderModified::dispatch($order);
        } else {
            OrderPlaced::dispatch($order);
        }

        $message = $update ? collect($messages)->reverse()->join('<br />') : 'Order created successfully.';

        if (Auth::user()->is_waiter && ! $update) {
            return redirect()->route('pos2.create')->with('success', $message);
        }

        return redirect()->route('pos2.create', $order->id)->with('success', $message);
    }

    public function print(Order $order)
    {
        $order->products;
        $params = [
            'order' => $order,
        ];

        return Inertia::render('Operation/POSReceipt', $params);
    }

    public function printed(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $order->is_printed = true;
        $order->save();

        return back();
    }
}
