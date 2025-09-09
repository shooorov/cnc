<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheCentralKitchen;
use App\Http\Cache\CacheProduct;
use App\Http\Cache\CacheProductCategory;
use App\Http\Cache\CacheProductRequisition;
use App\Http\Cache\CacheProductRequisitionItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItem as ResourcesMenuItem;
use App\Models\ProductRequisition;
use App\Models\ProductRequisitionItem;
use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductRequisitionController extends Controller
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
        $isDateSearch = RolePermission::isEnabled('record_search.product_requisition_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? now()->parse($request->end_date) : now();
            $start_date = $request->start_date ? now()->parse($request->start_date) : now()->startOfMonth();
        } else {
            $end_date = now();
            $start_date = now()->startOfMonth();
        }

        $product_requisitions = CacheProductRequisition::get()
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        $params = [
            'requisitions' => $product_requisitions,
            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ProductRequisition/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $items = CacheProduct::get();
        $params = [
            'date' => now()->format('Y-m-d'),
            'categories' => CacheProductCategory::get()->whereNull('product_category_id')->values(),
            'central_kitchens' => CacheCentralKitchen::get(),
            'items' => count($items) ? ResourcesMenuItem::collection($items)->hide([
                'unit_use',
                'min_limit',
                'in_stock',
                'category_name',
                'image',
                'image_default',
            ]) : [],
        ];

        return Inertia::render('Operation/ProductRequisition/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if (! UseBranch::id()) {
            return back()->with('fail', 'Please specify branch first.');
        }

        $request->validate([
            'requisition_date' => ['required', 'date'],
            'central_kitchen_id' => ['required', 'exists:central_kitchens,id'],

            'group_items.*.id' => ['required', 'exists:products,id'],
            'group_items.*.quantity' => ['nullable', 'numeric'],
            'group_items.*.avg_rate' => ['nullable', 'numeric'],
            'group_items.*.total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();

        $product_requisition = new ProductRequisition;
        $product_requisition->date = now()->parse($request->requisition_date);
        $product_requisition->total = $request->total;
        $product_requisition->branch_id = UseBranch::id();
        $product_requisition->central_kitchen_id = $request->central_kitchen_id;
        $product_requisition->save();

        foreach ($request->group_items ?? [] as $item) {
            $quantity = (int) $item['quantity'];
            if ($quantity > 0) {
                ProductRequisitionItem::create([
                    'quantity' => $item['quantity'],
                    'avg_rate' => $item['avg_rate'],
                    'total' => $item['total'],
                    'product_id' => $item['id'],
                    'product_requisition_id' => $product_requisition->id,
                ]);
            }
        }

        DB::commit();
        CacheProductRequisition::forget();
        CacheProductRequisitionItem::forget();

        return redirect()->route('product_requisition.index')->with('success', __('Requisition added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(ProductRequisition $product_requisition)
    {
        $product_requisition->products;
        $params = [
            'requisition' => $product_requisition,
        ];

        return Inertia::render('Operation/ProductRequisition/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(ProductRequisition $product_requisition)
    {
        $product_requisition->products;
        $params = [
            'requisition' => $product_requisition,
            'central_kitchens' => CacheCentralKitchen::get(),
        ];

        return Inertia::render('Operation/ProductRequisition/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, ProductRequisition $product_requisition)
    {
        $request->validate([
            'requisition_date' => ['required', 'date'],
            'central_kitchen_id' => ['required', 'exists:central_kitchens,id'],

            'group_items.*.item_id' => ['nullable', 'exists:products,id'],
            'group_items.*.quantity' => ['nullable', 'numeric'],
            'group_items.*.avg_rate' => ['nullable', 'numeric'],
            'group_items.*.total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();
        $product_requisition->date = now()->parse($request->requisition_date);
        $product_requisition->total = $request->total;
        $product_requisition->save();

        $previous_items = $product_requisition->products->pluck('id')->toArray();
        foreach ($request->group_items ?? [] as $item) {
            $quantity = array_key_exists('quantity', $item) ? $item['quantity'] : null;
            if ($quantity > 0) {
                $id = array_key_exists('requisition_item_id', $item) ? $item['requisition_item_id'] : null;
                if (($key = array_search($id, $previous_items)) !== false) {
                    unset($previous_items[$key]);
                }

                ProductRequisitionItem::updateOrCreate([
                    'product_id' => $item['id'],
                    'product_requisition_id' => $product_requisition->id,
                ], [
                    'quantity' => $quantity,
                    'avg_rate' => $item['avg_rate'],
                    'total' => $item['total'],
                ]);
            }
        }
        ProductRequisitionItem::where('product_requisition_id', $product_requisition->id)->whereIn('id', $previous_items)->delete();

        DB::commit();
        CacheProductRequisition::forget();
        CacheProductRequisitionItem::forget();

        return back()->with('success', __('Requisition modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(ProductRequisition $product_requisition)
    {
        DB::beginTransaction();
        foreach ($product_requisition->items as $item) {
            $item->delete();
        }
        $product_requisition->delete();
        DB::commit();
        CacheProductRequisition::forget();

        return redirect()->route('product_requisition.in')->with('success', __('Requisition removed successfully!'));
    }
}
