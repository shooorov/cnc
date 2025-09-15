<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheCentralKitchen;
use App\Http\Cache\CacheKitchenDelivery;
use App\Http\Cache\CacheKitchenDeliveryItem;
use App\Http\Cache\CacheProduct;
use App\Http\Cache\CacheProductRequisition;
use App\Http\Controllers\Controller;
use App\Http\Requests\KitchenDeliveryStoreRequest;
use App\Http\Requests\KitchenDeliveryUpdateRequest;
use App\Models\KitchenDelivery;
use App\Models\KitchenDeliveryItem;
use App\Models\Product;
use App\Models\ProductRequisition;
use App\RolePermission;
use Carbon\Carbon;
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
        foreach ($kitchen_deliveries as $item) {
            $item->date_format = $item->date->format('d/m/Y');
            $item->branch_name = $item->branch->name;
            $item->name = $item->name . ' - ' . $item->date_format;
        }

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
        $requisition_items = $requisitions->mapWithKeys(function ($requisition) {
            $requisition->name = $requisition->branch->name . ' - ' . $requisition->date->format('d/m/Y');
            return [$requisition->id => $requisition->products];
        });
        // dd($requisition_items);

        return Inertia::render('Operation/KitchenDelivery/Create', [
            'date' => now()->format('Y-m-d'),
            'requisitions' => $requisitions,
            'central_kitchens' => CacheCentralKitchen::get(),
            'items' => $items,
            'requisition_items' => $requisition_items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(KitchenDeliveryStoreRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();

        $data = $request->validated();

        $kitchen_delivery = new KitchenDelivery;
        $kitchen_delivery->date = Carbon::parse($data['delivery_date']); // <-- safe
        $kitchen_delivery->total = $data['total'];
        $kitchen_delivery->branch_id = ProductRequisition::find($data['requisition_id'])->branch_id;
        $kitchen_delivery->product_requisition_id = $data['requisition_id'];
        $kitchen_delivery->central_kitchen_id = $data['central_kitchen_id'];
        $kitchen_delivery->save();

        foreach ($data['group_items'] ?? [] as $item) {
            $product_id = $item['product_id'] ?? null;
            $rate = $item['rate'] ?? null;
            $avg_rate = $item['avg_rate'] ?? null;
            $total = $item['delivery_total'] ?? null;
            $delivery_quantity = $item['delivery_quantity'] ?? null;
            $requisition_quantity = $item['requisition_quantity'] ?? null;

            if ($product_id) {
                KitchenDeliveryItem::create([
                    'delivery_quantity' => $delivery_quantity,
                    'requisition_quantity' => $requisition_quantity,
                    'rate' => $rate,
                    'avg_rate' => $avg_rate,
                    'total' => $total,
                    'product_id' => $product_id,
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
        $kitchen_delivery->load('items', 'branch');
        // dd($kitchen_delivery->items);
        $kitchen_delivery->date_format = $kitchen_delivery->date->format('d/m/Y');
        $kitchen_delivery->branch_name = $kitchen_delivery->branch->name;
        $kitchen_delivery->name = $kitchen_delivery->name . ' - ' . $kitchen_delivery->date_format;

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
            $requisition->name = $requisition->branch->name . ' - ' . $requisition->date->format('d/m/Y');
            $requisition->items = $requisition->items;
        }

        $delivery_items = $kitchen_delivery->items;
        $kitchen_delivery->date_format = $kitchen_delivery->date->format('Y-m-d');
        // dd($kitchen_delivery);
        $params = [
            'items' => Product::get(['id', 'name']),
            'requisitions' => $requisitions,
            'delivery_items' => $delivery_items,
            'kitchen_delivery' => $kitchen_delivery,
            'central_kitchens' => CacheCentralKitchen::get(),
        ];

        return Inertia::render('Operation/KitchenDelivery/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(KitchenDeliveryUpdateRequest $request, KitchenDelivery $kitchen_delivery)
    {
        // dd($request->all());
        $data = $request->validated();

        DB::beginTransaction();

        // Update kitchen delivery main info
        $kitchen_delivery->update([
            'date' => Carbon::parse($data['delivery_date']),
            'total' => $data['total'],
        ]);

        // Keep track of existing item IDs
        $existingItemIds = $kitchen_delivery->items->pluck('id')->toArray();

        foreach ($data['group_items'] ?? [] as $item) {
            $itemId = $item['id'] ?? null;
            $productId = $item['product_id'] ?? null;

            // Only proceed if product_id exists
            if ($productId) {
                // If delivery_quantity is more than 0, update or create
                KitchenDeliveryItem::updateOrCreate(
                    ['id' => $itemId, 'kitchen_delivery_id' => $kitchen_delivery->id],
                    [
                        'product_id' => $productId,
                        'delivery_quantity' => $item['delivery_quantity'] ?? 0,
                        'requisition_quantity' => $item['requisition_quantity'] ?? 0,
                        'rate' => $item['rate'] ?? 0,
                        'avg_rate' => $item['avg_rate'] ?? 0,
                        'total' => $item['delivery_total'] ?? 0,
                    ]
                );

                // Remove this ID from existing items to keep track of deletions
                if ($itemId) {
                    $key = array_search($itemId, $existingItemIds);
                    if ($key !== false) {
                        unset($existingItemIds[$key]);
                    }
                }
            }
        }

        // Delete items that were removed from the form
        if (!empty($existingItemIds)) {
            KitchenDeliveryItem::where('kitchen_delivery_id', $kitchen_delivery->id)
                ->whereIn('id', $existingItemIds)
                ->delete();
        }

        DB::commit();

        // Clear cache
        CacheKitchenDelivery::forget();
        CacheKitchenDeliveryItem::forget();

        return redirect()
            ->route('kitchen_delivery.index')
            ->with('success', __('Requisition updated successfully!'));
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

        return redirect()->route('kitchen_delivery.index')->with('success', __('Requisition removed successfully!'));
    }
}
