<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheCentralKitchen;
use App\Http\Cache\CacheKitchenDelivery;
use App\Http\Cache\CacheKitchenDeliveryItem;
use App\Http\Cache\CacheProduct;
use App\Http\Cache\CacheProductRequisition;
use App\Http\Controllers\Controller;
use App\Models\KitchenDelivery;
use App\Models\KitchenDeliveryItem;
use App\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KitchenDeliveryController extends Controller
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $isDateSearch = RolePermission::isEnabled('record_search.kitchen_delivery_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? now()->parse($request->end_date) : now();
            $start_date = $request->start_date ? now()->parse($request->start_date) : now()->startOfMonth();
        } else {
            $end_date = now();
            $start_date = now()->startOfMonth();
        }

        $kitchen_deliveries = CacheKitchenDelivery::get()
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        $params = [
            'kitchen_deliveries' => $kitchen_deliveries,
            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/KitchenDelivery/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $items = CacheProduct::get();
        $requisitions = CacheProductRequisition::get();
        $requisition_items = [];
        foreach ($requisitions as $requisition) {
            $requisition->name = $requisition->branch->name . ' - '. $requisition->date->format('d/m/Y');
            $requisition_items[$requisition->id] = $requisition->products;
        }
        // dd($requisition_items);
        $params = [
            'date' => now()->format('Y-m-d'),
            'requisitions' => $requisitions,
            'central_kitchens' => CacheCentralKitchen::get(),

            'items' => $items,
            'requisition_items' => $requisition_items,
        ];

        return Inertia::render('Operation/KitchenDelivery/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_date' => ['required', 'date'],
            'branch_id' => ['required', 'exists:branches,id'],
            'requisition_id' => ['required', 'exists:product_requisitions,id'],
            'central_kitchen_id' => ['required', 'exists:central_kitchens,id'],
            'total' => ['required', 'numeric'],

            'group_items.*.id' => ['required', 'exists:products,id'],
            'group_items.*.delivery_quantity' => ['nullable', 'numeric'],
            'group_items.*.avg_rate' => ['nullable', 'numeric'],
            'group_items.*.delivery_total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();

        $kitchen_delivery = new KitchenDelivery;
        $kitchen_delivery->date = now()->parse($request->delivery_date);
        $kitchen_delivery->total = $request->total;
        $kitchen_delivery->branch_id = $request->branch_id;
        $kitchen_delivery->requisition_id = $request->requisition_id;
        $kitchen_delivery->central_kitchen_id = $request->central_kitchen_id;
        $kitchen_delivery->save();

        foreach ($request->group_items ?? [] as $item) {
            $delivery_quantity = array_key_exists('delivery_quantity', $item) ? $item['delivery_quantity'] : null;
            if ($delivery_quantity > 0) {
                KitchenDeliveryItem::create([
                    'delivery_quantity' => $item['delivery_quantity'],
                    'requisition_quantity' => $item['requisition_quantity'],
                    'avg_rate' => $item['avg_rate'],
                    'total' => $item['delivery_total'],
                    'item_id' => $item['id'],
                    'kitchen_delivery_id' => $kitchen_delivery->id,
                ]);
            }
        }

        DB::commit();
        CacheKitchenDelivery::forget();
        CacheKitchenDeliveryItem::forget();

        return redirect()->route('kitchen_delivery.index')->with('success', __('Requisition added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(KitchenDelivery $kitchen_delivery)
    {
        $kitchen_delivery->items;
        $params = [
            'kitchen_delivery' => $kitchen_delivery,
        ];

        return Inertia::render('Operation/KitchenDelivery/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(KitchenDelivery $kitchen_delivery)
    {
        $requisitions = CacheProductRequisition::get();

        foreach ($requisitions as $requisition) {
            $requisition->items = $requisition->items;
        }

        $params = [
            'date' => now()->format('Y-m-d'),
            'requisitions' => $requisitions,
            'central_kitchens' => CacheCentralKitchen::get(),
            'kitchen_delivery' => $kitchen_delivery,
        ];

        return Inertia::render('Operation/KitchenDelivery/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, KitchenDelivery $kitchen_delivery)
    {
        $request->validate([
            'delivery_date' => ['required', 'date'],
            'branch_id' => ['required', 'exists:branches,id'],
            'requisition_id' => ['required', 'exists:product_requisitions,id'],
            'central_kitchen_id' => ['required', 'exists:central_kitchens,id'],
            'total' => ['nullable', 'numeric'],

            'group_items.*.id' => ['nullable', 'exists:products,id'],
            'group_items.*.delivery_quantity' => ['nullable', 'numeric'],
            'group_items.*.avg_rate' => ['nullable', 'numeric'],
            'group_items.*.delivery_total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();
        $kitchen_delivery->date = now()->parse($request->delivery_date);
        $kitchen_delivery->total = $request->total;
        $kitchen_delivery->save();

        $previous_items = $kitchen_delivery->items->pluck('id')->toArray();
        foreach ($request->group_items ?? [] as $item) {
            $delivery_quantity = array_key_exists('delivery_quantity', $item) ? $item['delivery_quantity'] : null;
            if ($delivery_quantity > 0) {
                $id = array_key_exists('delivery_item_id', $item) ? $item['delivery_item_id'] : null;
                if (($key = array_search($id, $previous_items)) !== false) {
                    unset($previous_items[$key]);
                }

                KitchenDeliveryItem::updateOrCreate([
                    'item_id' => $item['id'],
                    'kitchen_delivery_id' => $kitchen_delivery->id,
                ], [
                    'delivery_quantity' => $delivery_quantity,
                    'requisition_quantity' => $item['requisition_quantity'],
                    'avg_rate' => $item['avg_rate'],
                    'total' => $item['delivery_total'],
                ]);
            }
        }
        KitchenDeliveryItem::where('kitchen_delivery_id', $kitchen_delivery->id)->whereIn('id', $previous_items)->delete();

        DB::commit();
        CacheKitchenDelivery::forget();
        CacheKitchenDeliveryItem::forget();

        return back()->with('success', __('Requisition modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(KitchenDelivery $kitchen_delivery)
    {
        DB::beginTransaction();
        foreach ($kitchen_delivery->items as $item) {
            $item->delete();
        }
        $kitchen_delivery->delete();
        DB::commit();
        CacheKitchenDelivery::forget();

        return redirect()->route('kitchen_delivery.in')->with('success', __('Requisition removed successfully!'));
    }
}
