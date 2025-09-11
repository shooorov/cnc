<?php

namespace App\Http\Controllers\Operation;

use App\Events\OrderModified;
use App\Helpers;
use App\Http\Cache\CacheCustomer;
use App\Http\Cache\CacheCustomerOrder;
use App\Http\Cache\CacheOrder;
use App\Http\Cache\CacheOrderProductQuantity;
use App\Http\Cache\CachePaymentMethod;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\CustomerToken;
use App\Models\Order;
use App\Models\OrderApproval;
use App\Models\OrderDelivery;
use App\Models\OrderHistory;
use App\Models\OrderPaymentMethod;
use App\Models\OrderProduct;
use App\Models\OrderProductTopping;
use App\Models\ProductInventory;
use App\Models\Production;
use App\Models\Status;
use App\Models\User;
use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
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
     * Show the Order list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $status = $request->status;
        $isDateSearch = RolePermission::isEnabled('record_search.order_date_search');
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');

        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : null;
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : null;
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $customers = CacheCustomer::get()->map(function ($i) {
            $name = [
                $i->mobile,
                $i->name,
            ];

            return [
                'id' => $i->id,
                'name' => collect($name)->filter(fn($i) => $i)->join(' / '),
            ];
        });
        $payment_methods = CachePaymentMethod::get();
        if ($isThirtyparcenton && !$request->user()->is_admin) {
            $order_managers = Order::select('manager_id')
                ->where('vat_add', 1) // Adding the condition for vat_add = 1
                ->groupBy('manager_id')
                ->pluck('manager_id')
                ->toArray();
        } else {
            $order_managers = Order::select('manager_id')
                ->groupBy('manager_id')
                ->pluck('manager_id')
                ->toArray();
        }

        // $order_managers = Order::select('manager_id')
        // 	->groupBy('manager_id')
        // 	->pluck('manager_id')
        // 	->toArray();

        $managers = CacheUser::get()->whereIn('id', $order_managers)->values()->all();

        $waiters = User::whereIsWaiter(true)->get();

        $order = new Order;

        $statuses = collect($order->statuses)->map(function ($status, $index) {
            return [
                'id' => $index,
                'name' => $status,
            ];
        })->values()->toArray();

        $params = [
            'customers' => $customers,
            'payment_methods' => $payment_methods,
            'managers' => $managers,
            'waiters' => $waiters,
            'statuses' => $statuses,

            'filter' => [
                'manager_id' => $request->manager_id ?? '',
                'waiter_id' => $request->waiter_id ?? '',
                'customer_id' => $request->customer_id ?? '',
                'status' => $status,
                'payment_method_id' => $request->payment_method_id ?? '',
                'end_date' => $request->end_date ? Helpers::operationDay($end_date) : null,
                'start_date' => $request->start_date ? Helpers::operationDay($start_date) : null,
            ],
        ];

        return Inertia::render('Operation/Order/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function load(Request $request)
    {
        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? -1);

        $filter = $request->search['value'] ?? null;
        $sort_dir = $request->order[0]['dir'] ?? 'asc';
        $sort_column = $request->order[0]['column'] ?? 1;

        $status = $request->status;
        $manager_id = $request->manager_id;
        $waiter_id = $request->waiter_id;
        $customer_id = $request->customer_id;
        $payment_method_id = $request->payment_method_id;

        $isDateSearch = RolePermission::isEnabled('record_search.order_date_search');
        $isThirtyparcenton = RolePermission::isEnabled('one_third.feature_on');

        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : null;
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : null;
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $columns = [
            0 => null,
            1 => 'orders.date',
            2 => 'orders.invoice_number',
            3 => 'customers.name',
            4 => 'orders.discount_amount',
            5 => 'orders.vat_amount',
            6 => 'orders.total',
            7 => 'payment_methods.name',
        ];

        $baseQuery = Order::with('products.product')
            ->select(
                'orders.*',
                'customers.name as customer_name',
                'payment_methods.name as payment_method_name',
                'manager.name as manager_name',
                'waiter.name as waiter_name'
            )
            ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'orders.payment_method_id')
            ->leftJoin('users as manager', 'manager.id', '=', 'orders.manager_id')
            ->leftJoin('users as waiter', 'waiter.id', '=', 'orders.waiter_id');

        // Filters
        if ($start_date) $baseQuery->where('orders.date', '>=', $start_date);
        if ($end_date) $baseQuery->where('orders.date', '<=', $end_date);
        if ($manager_id) $baseQuery->where('orders.manager_id', $manager_id);
        if ($waiter_id) $baseQuery->where('orders.waiter_id', $waiter_id);
        if ($status) $baseQuery->where('orders.status', $status);
        if ($customer_id) $baseQuery->where('orders.customer_id', $customer_id);
        if ($payment_method_id) $baseQuery->where('orders.payment_method_id', $payment_method_id);
        if ($isThirtyparcenton && !$request->user()->is_admin) $baseQuery->where('orders.vat_add', 1);

        $recordsTotal = (clone $baseQuery)->count();

        $query = clone $baseQuery;

        if ($request->columns && is_array($request->columns)) {
            foreach ($request->columns as $index => $col) {
                $colSearch = $col['search']['value'] ?? null;
                $colName = $columns[$index] ?? null;
                if ($colSearch && $colName) $query->where($colName, 'like', '%' . $colSearch . '%');
            }
        }

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('orders.invoice_number', 'like', '%' . $filter . '%')
                    ->orWhere('customers.name', 'like', '%' . $filter . '%')
                    ->orWhere('manager.name', 'like', '%' . $filter . '%')
                    ->orWhere('waiter.name', 'like', '%' . $filter . '%')
                    ->orWhere('payment_methods.name', 'like', '%' . $filter . '%')
                    ->orWhere('orders.total', 'like', '%' . $filter . '%');
            });
        }

        $recordsFiltered = (clone $query)->count();

        if (!empty($columns[$sort_column])) $query->orderBy($columns[$sort_column], $sort_dir);
        else $query->orderBy('orders.date', 'desc');

        if ($length > 0) $query->offset($start)->limit($length);

        $records = $query->get();

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $records->map(function ($order) {
                $actions = [];
                $actions['print_url'] = RolePermission::isRouteValid('pos.print') ? route('pos.print', $order->id) : null;
                $actions['detail_url'] = RolePermission::isRouteValid('order.show') ? route('order.show', $order->id) : null;
                $actions['edit_url'] = UseBranch::id() && RolePermission::isRouteValid('pos.create') ? route('pos.create', $order->id) : null;
                $actions['destroy_url'] = UseBranch::id() && RolePermission::isRouteValid('order.destroy') ? route('order.destroy', $order->id) : null;

                return [
                    'id'                    => $order->id,
                    'branch_name'           => $order->branch->name,
                    'waiter_name'           => $order->waiter_name,
                    'invoice_number'        => $order->invoice_number,
                    'branch_invoice'        => $order->branch->name . '<br>' . $order->invoice_number,
                    'datetime_format'       => $order->datetime_format,
                    'payment_method_name'   => $order->payment_method_name,
                    'discount_amount'       => $order->discount_amount,
                    'discount_type'         => $order->discount_type,
                    'member_code'           => $order->member_code,
                    'member_discount'       => $order->member_discount,
                    'is_complete'           => $order->status === 'complete' || !empty($order->cash),
                    'detail'                => collect([$order->description, $order->payment_method_name])->filter(fn($i) => $i)->join('<br>'),
                    'description'           => $order->description,
                    'vat_amount'            => $order->vat_amount,
                    'total'                 => $order->total,
                    'status'                => $order->status,
                    'products'              => $order->products->map(fn($p) => ['name' => $p->product_name, 'quantity' => $p->quantity]),
                    'actions'               => $actions,
                ];
            }),
        ]);
    }

    /**
     * Show the Order detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Order $order)
    {
        $params = [
            'order' => $order,
            'statuses' => $order->statuses,
        ];

        return Inertia::render('Operation/Order/Show', $params);
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Order $order)
    {
        if ($order->status == 'complete' && ! RolePermission::isEnabled('POS.delete_completed_order')) {
            return back()->with('fail', 'Order removing denied! Order status complete');
        }

        DB::beginTransaction();

        try {
            ProductInventory::where('order_id', $order->id)->delete();

            OrderApproval::where('order_id', $order->id)->delete();

            OrderDelivery::where('order_id', $order->id)->delete();

            OrderHistory::where('order_id', $order->id)->delete();

            OrderPaymentMethod::where('order_id', $order->id)->delete();

            OrderProduct::where('order_id', $order->id)->delete();

            OrderProductTopping::where('order_id', $order->id)->delete();

            Production::where('order_id', $order->id)->delete();

            CustomerToken::where('order_id', $order->id)->delete();

            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw ($e);

            return back()->withInput()->with('fail', __('Order removing request failed!'));
        }

        CacheOrderProductQuantity::forget();
        CacheCustomerOrder::forget();

        return redirect()->route('order.index')->with('success', __('Order removed successfully!'));
    }

    /**
     * Change status the specified resource in storage.
     *
     * @return Response
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (! array_key_exists($request->status, $order->statuses)) {
            return back()->with('fail', 'Status changing request failed! Invalid status!');
        }

        if ($order->status == $request->status) {
            return back()->with('fail', 'Status already changed!');
        }

        DB::beginTransaction();
        $order->changeStatuses()->save(new Status([
            'previous_status' => $order->status ?? '',
            'changed_status' => $request->status,
            'user_id' => $request->user()->id,
        ]));
        $order->status = $request->status;
        $order->save();

        DB::commit();

        OrderModified::dispatch($order);

        CacheOrder::forget();

        return back()->with('success', 'Status changed to "' . $order->statuses[$request->status] . '" successfully');
    }
}
