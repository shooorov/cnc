<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheItem;
use App\Http\Cache\CacheItemCategory;
use App\Http\Cache\CacheRequisition;
use App\Http\Cache\CacheRequisitionItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\Item as ResourcesItem;
use App\Http\Resources\Requisition as ResourcesRequisition;
use App\Models\Item;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RequisitionController extends Controller
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
        $isDateSearch = RolePermission::isEnabled('record_search.item_requisition_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? now()->parse($request->end_date) : now();
            $start_date = $request->start_date ? now()->parse($request->start_date) : now()->startOfMonth();
        } else {
            $end_date = now();
            $start_date = now()->startOfMonth();
        }

        $requisitions = CacheRequisition::get()
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        $items = CacheItem::get();

        $params = [
            'items' => count($items) ? ResourcesItem::collection($items) : [],
            'requisitions' => $requisitions,
            // 'requisitions'      => count($requisitions) ? ResourcesRequisition::collection($requisitions) : [],
            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/Requisition/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $items = CacheItem::get();
        $params = [
            'date' => now()->format('Y-m-d'),
            'units' => (new Item)->units,
            'categories' => CacheItemCategory::get()->whereNull('item_category_id')->values(),
            'items' => count($items) ? ResourcesItem::collection($items)->hide([
                'unit_use',
                'min_limit',
                'in_stock',
                'category_name',
                'image',
                'image_default',
            ]) : [],
        ];

        return Inertia::render('Operation/Requisition/Create', $params);
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
            'group_items.*.id' => ['required', 'exists:items,id'],
            'group_items.*.quantity' => ['nullable', 'numeric'],
            'group_items.*.avg_rate' => ['nullable', 'numeric'],
            'group_items.*.total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();

        $requisition = new Requisition;
        $requisition->date = now()->parse($request->requisition_date);
        $requisition->total = $request->total;
        $requisition->branch_id = UseBranch::id();
        $requisition->save();

        foreach ($request->group_items ?? [] as $item) {
            $quantity = array_key_exists('quantity', $item) ? $item['quantity'] : null;
            if ($quantity > 0) {
                RequisitionItem::create([
                    'quantity' => $item['quantity'],
                    'avg_rate' => $item['avg_rate'],
                    'total' => $item['total'],
                    'item_id' => $item['id'],
                    'requisition_id' => $requisition->id,
                ]);
            }
        }

        DB::commit();
        CacheRequisition::forget();
        CacheRequisitionItem::forget();

        return redirect()->route('requisition.index')->with('success', __('Requisition added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Requisition $requisition)
    {
        $requisition->items;
        $params = [
            'requisition' => $requisition,
        ];

        return Inertia::render('Operation/Requisition/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Requisition $requisition)
    {
        $requisition->items;
        $params = [
            'requisition' => $requisition,
        ];

        return Inertia::render('Operation/Requisition/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Requisition $requisition)
    {
        $request->validate([
            'requisition_date' => ['required', 'date'],
            'group_items.*.item_id' => ['nullable', 'exists:items,id'],
            'group_items.*.quantity' => ['nullable', 'numeric'],
            'group_items.*.avg_rate' => ['nullable', 'numeric'],
            'group_items.*.total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();
        $requisition->date = now()->parse($request->requisition_date);
        $requisition->total = $request->total;
        $requisition->save();

        $previous_items = $requisition->items->pluck('id')->toArray();
        foreach ($request->group_items ?? [] as $item) {
            $quantity = array_key_exists('quantity', $item) ? $item['quantity'] : null;
            if ($quantity > 0) {
                $id = array_key_exists('requisition_item_id', $item) ? $item['requisition_item_id'] : null;
                if (($key = array_search($id, $previous_items)) !== false) {
                    unset($previous_items[$key]);
                }

                RequisitionItem::updateOrCreate([
                    'item_id' => $item['id'],
                    'requisition_id' => $requisition->id,
                ], [
                    'quantity' => $quantity,
                    'avg_rate' => $item['avg_rate'],
                    'total' => $item['total'],
                ]);
            }
        }
        RequisitionItem::where('requisition_id', $requisition->id)->whereIn('id', $previous_items)->delete();

        DB::commit();
        CacheRequisition::forget();
        CacheRequisitionItem::forget();

        return back()->with('success', __('Requisition modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Requisition $requisition)
    {
        DB::beginTransaction();
        foreach ($requisition->items as $item) {
            $item->delete();
        }
        $requisition->delete();
        DB::commit();
        CacheRequisition::forget();

        return redirect()->route('requisition.in')->with('success', __('Requisition removed successfully!'));
    }
}
