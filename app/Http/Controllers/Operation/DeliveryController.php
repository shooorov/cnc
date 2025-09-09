<?php

namespace App\Http\Controllers\Operation;

use App\Events\OrderModified;
use App\Helpers;
use App\Http\Cache\CacheCustomer;
use App\Http\Cache\CacheUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDeliveryCollection;
use App\Models\Order;
use App\Models\Status;
use App\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DeliveryController extends Controller
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
        $isDateSearch = RolePermission::isEnabled('record_search.delivery_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : null;
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : null;
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $customers = CacheCustomer::get();

        $order_creators = Order::select('creator_id')->groupBy('creator_id')->pluck('creator_id')->toArray();
        $creators = CacheUser::get()->whereIn('id', $order_creators)->values()->all();
        $riders = CacheUser::get()->where('is_rider', true)->values()->all();

        $params = [
            'customers' => $customers,
            'creators' => $creators,
            'riders' => $riders,

            'filter' => [
                'creator_id' => $request->creator_id ?? '',
                'rider_id' => $request->rider_id ?? '',
                'customer_id' => $request->customer_id ?? '',
                'end_date' => $request->end_date ? Helpers::operationDay($end_date) : null,
                'start_date' => $request->start_date ? Helpers::operationDay($start_date) : null,
            ],
        ];

        return Inertia::render('Operation/Delivery/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function load(Request $request)
    {
        $start = $request->start ?? 0;
        $length = $request->length ?? -1;

        $filter = $request->search['value'];
        $sort_dir = $request->order[0]['dir'];
        $sort_column = $request->order[0]['column'];

        $rider_id = $request->rider_id;
        $customer_id = $request->customer_id;

        $isDateSearch = RolePermission::isEnabled('record_search.delivery_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : null;
            $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : null;
        } else {
            $end_date = Helpers::dayEndedAt();
            $start_date = Helpers::dayStartedAt();
        }

        $records = Order::leftJoin('order_deliveries', 'order_deliveries.order_id', '=', 'orders.id')
            ->leftJoin('customers', 'order_deliveries.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'order_deliveries.rider_id', '=', 'users.id')
            ->select('orders.*')
            ->whereNotNull('order_deliveries.id')
            ->when($start_date, function ($query, $start_date) {
                $query->where('orders.date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->where('orders.date', '<=', $end_date);
            })
            ->when($rider_id, function ($query, $rider_id) {
                $query->where('order_deliveries.rider_id', $rider_id);
            })
            ->when($customer_id, function ($query, $customer_id) {
                $query->where('order_deliveries.customer_id', $customer_id);
            });

        $recordsTotal = (clone $records)->count();

        $records->where(function ($query) use ($filter) {
            $query->where('orders.number', 'like', '%'.$filter.'%')
                ->orWhere('orders.date', 'like', '%'.$filter.'%')
                ->orWhere('order_deliveries.address', 'like', '%'.$filter.'%')
                ->orWhere('customers.mobile', 'like', '%'.$filter.'%')
                ->orWhere('customers.name', 'like', '%'.$filter.'%')
                ->orWhere('users.name', 'like', '%'.$filter.'%');
        });

        $recordsFiltered = (clone $records)->count();

        $columns = [
            'order_deliveries.id',
            'orders.number',
            'orders.date',
            'order_deliveries.address',
            'customers.name',
            'users.name',
            '',
        ];

        $records->orderBy($columns[$sort_column], $sort_dir);

        if ($length > 0) {
            $records->offset($start)->limit($length);
        }
        // dd($records->get());
        $record_collection = new OrderDeliveryCollection($records->get());
        $record_collection->draw = intval($request->draw);
        $record_collection->recordsFiltered = $recordsFiltered;
        $record_collection->recordsTotal = $recordsTotal;

        return $record_collection;
    }

    /**
     * Show the Order detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Order $order)
    {
        $orders = Order::withoutGlobalScope(UseBranchScope::class)
            ->leftJoin('order_deliveries', 'order_deliveries.order_id', '=', 'orders.id')
            ->leftJoin('customers', 'order_deliveries.customer_id', '=', 'customers.id')
            ->leftJoin('users', 'order_deliveries.rider_id', '=', 'users.id')
            ->select('orders.*')
            ->whereNotNull('order_deliveries.id')
            ->where('order_deliveries.status', '!=', 'delivered')
            ->get();

        $params = [
            'orders' => $orders,
            'order' => $order,
        ];

        return Inertia::render('Operation/Delivery/Show', $params);
    }

    /**
     * Change status the specified resource in storage.
     *
     * @return Response
     */
    public function updateStatus(Request $request, Order $order)
    {
        $order_delivery = $order->order_delivery;
        DB::beginTransaction();
        $previous_status = $order_delivery->status;

        if (empty($order_delivery->accepted_at)) {
            $order_delivery->status = 'accept';
            $order_delivery->accepted_at = now();
        } elseif (empty($order_delivery->collected_at)) {
            $order_delivery->status = 'collect';
            $order_delivery->collected_at = now();
        } elseif (empty($order_delivery->delivered_at)) {
            $order_delivery->status = 'delivered';
            $order_delivery->delivered_at = now();
        } else {
            return back()->with('fail', 'Order already delivered!');
        }

        $order_delivery->changeStatuses()->save(new Status([
            'previous_status' => $previous_status,
            'changed_status' => $order_delivery->status,
            'user_id' => $request->user()->id,
        ]));

        $order_delivery->save();

        DB::commit();

        OrderModified::dispatch($order);

        return back()->with('success', 'Status changed to "'.$order_delivery->statuses[$order_delivery->status].'" successfully');
    }
}
