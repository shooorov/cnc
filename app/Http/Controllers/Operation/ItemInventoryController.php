<?php

namespace App\Http\Controllers\Operation;

use App\Formula;
use App\Helpers;
use App\Http\Cache\CacheBranch;
use App\Http\Cache\CacheDocument;
use App\Http\Cache\CacheItem;
use App\Http\Cache\CacheItemInventory;
use App\Http\Cache\CacheItemInventoryItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\Item as ResourcesItem;
use App\Models\Document;
use App\Models\Item;
use App\Models\ItemInventory;
use App\Models\ItemInventoryItem;
use App\RolePermission;
use App\UseBranch;
use App\UseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ItemInventoryController extends Controller
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
        $end_date = $request->end_date ? Helpers::dayEndedAt($request->end_date) : Helpers::dayEndedAt(now());
        $start_date = $request->start_date ? Helpers::dayStartedAt($request->start_date) : Helpers::dayStartedAt(now()->startOfMonth());

        $item_id = $request->item_id;

        $item_inventories = CacheItemInventory::get()
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        $items = CacheItem::get();
        $params = [
            'items' => count($items) ? ResourcesItem::collection($items) : [],
            'item_inventories' => $item_inventories,
            'direction' => 'in',

            'end_date' => $end_date->format('d/m/Y'),
            'start_date' => $start_date->format('d/m/Y'),

            'filter' => [
                'item_id' => $item_id,
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ItemInventory/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function compare(Request $request)
    {
        $isDateSearch = RolePermission::isEnabled('record_search.inventory_compare_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? now()->parse($request->end_date)->endOfDay() : now();
            $start_date = $request->start_date ? now()->parse($request->start_date) : now()->startOfMonth();
        } else {
            $end_date = now();
            $start_date = now()->startOfMonth();
        }

        $direction = 'in';

        // Cache lookups
        $cache_branches = CacheBranch::get();
        $cache_items = CacheItem::get();

        // Filter inventories (with date + direction)
        $cache_item_inventories = CacheItemInventory::get()
            ->where('direction', $direction)
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        // Index inventories by ID for faster lookup
        $inventoriesById = $cache_item_inventories->keyBy('id');

        // Only include inventory items belonging to filtered inventories
        $cache_inventory_items = CacheItemInventoryItem::whereIn(
            $cache_item_inventories->pluck('id'),
            'item_inventory_id'
        );

        // Group items by item_id + branch_id
        $items_array = [];
        foreach ($cache_inventory_items as $inv_item) {
            $parent = $inventoriesById->get($inv_item->item_inventory_id);

            if (!$parent) {
                continue; // skip if no matching parent inventory
            }

            $branch_id = $parent->branch_id;
            $item_id   = $inv_item->item_id;

            $items_array[$item_id][$branch_id][] = [
                'rate'  => $inv_item->rate,
                'total' => $inv_item->total,
            ];
        }
        ksort($items_array, SORT_NUMERIC);

        // Build final list
        $items_list = [];
        foreach ($items_array as $item_id => $array) {
            $item = $cache_items->first(fn($i) => $i->id == $item_id);

            $branches = [];
            foreach ($cache_branches as $branch) {
                if (array_key_exists($branch->id, $array)) {
                    $records   = $array[$branch->id];
                    $rates     = array_column($records, 'rate');
                    $sum_total = array_sum(array_column($records, 'total'));

                    $rates = array_filter($rates); // remove null/empty
                    if (count($rates) === 0) {
                        $avg_rate = 0;
                    } else {
                        $avg_rate = number_format(array_sum($rates) / count($rates), 2, '.', '');
                    }

                    $branches[$branch->id] = [
                        'rates'    => implode(', ', $rates),
                        'avg_rate' => $avg_rate ? "(avg. $avg_rate)" : '',
                        'total'    => $sum_total,
                    ];
                } else {
                    $branches[$branch->id] = [
                        'rates'    => 'N/A',
                        'avg_rate' => '',
                        'total'    => 'N/A',
                    ];
                }
            }

            $item->branches = $branches;
            $items_list[]   = $item;
        }

        $params = [
            'items'      => $cache_items,
            'direction'  => $direction,
            'items_list' => $items_list,
            'end_date'   => $end_date->format('d/m/Y'),
            'start_date' => $start_date->format('d/m/Y'),
            'filter'     => [
                'end_date'   => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ItemInventory/Compare', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function activities(Request $request)
    {
        $end_date = now()->parse($request->end_date ?? now())->endOfDay();
        $start_date = now()->parse($request->start_date ?? now()->subDays(3))->startOfDay();
        $item_id = $request->item_id;
        $direction = 'in';
        // dd(
        //     $end_date,
        //     $start_date,
        //     $item_id
        // );
        $cache_items = CacheItem::get();
        $cache_branches = CacheBranch::get();
        $cache_item_inventories = CacheItemInventory::get()->where('direction', $direction)->where('date', '>=', $start_date)->where('date', '<=', $end_date);
        $cache_inventory_items = CacheItemInventoryItem::get()->where('direction', $direction); // direction out contains rate n total null

        $item_inventory_ids = $inventory_items = [];
        foreach ($cache_item_inventories as $item) {
            $item_inventory_ids[] = $item->id;
        }
        $filtered_inventory_items = $cache_inventory_items->whereIn('item_inventory_id', $item_inventory_ids);
        foreach ($filtered_inventory_items as $item) {
            $inventory_items[] = $item;
        }

        $items_array = $items_list = [];
        foreach ($inventory_items as $inv_item) {
            $inventory = $cache_item_inventories->first(fn($i) => $i->id == $inv_item['item_inventory_id']);
            $items_array[$inventory->branch_id][] = $inv_item;
        }

        ksort($items_array, 1); // 1 = SORT_NUMERIC

        $max_row_count = 0;
        foreach ($cache_branches as $branch) {
            if (! array_key_exists($branch->id, $items_array)) {
                $items_array[$branch->id] = [];
            }
            $count = count($items_array[$branch->id]);
            if ($max_row_count < $count) {
                $max_row_count = $count;
            }
        }
        $iteration = [];
        for ($i = 0; $i < $max_row_count; $i++) {
            $iteration[] = $i;
            foreach ($cache_branches as $branch) {
                $items_list[$branch->id][$i] = count($items_array[$branch->id]) > $i ? $items_array[$branch->id][$i] : [
                    'rate' => '',
                    'total' => '',
                    'inventory_date_format' => 'N/A',
                    'required_quantity_unit' => '',
                ];
            }
        }

        $params = [
            'items' => $cache_items,
            'direction' => $direction,
            'iteration' => $iteration,
            'inventory_items' => $items_list,

            'end_date' => $end_date->format('d/m/Y'),
            'start_date' => $start_date->format('d/m/Y'),

            'filter' => [
                'item_id' => $item_id,
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ItemInventory/Activity', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function in(Request $request)
    {
        $end_date = now()->parse($request->end_date ?? now())->endOfDay();
        $start_date = now()->parse($request->start_date ?? now()->subDays(2))->startOfDay();
        $direction = 'in';

        $items = CacheItem::get();
        $inv_ids = CacheItemInventory::get()->where('direction', $direction)
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)->values()->pluck('id')->all();

        // dd($item_inventories);
        // Try part =>
        $inventory_items = CacheItemInventoryItem::whereIn($inv_ids, 'item_inventory_id');
        $inventories = [];
        foreach ($inventory_items as $item) {
            $inventories[$item->item_inventory_id][] = $item;
        }
        $item_inventories = [];
        foreach ($inventories as $inv => $items) {
            $item_inv = CacheItemInventory::find($inv);
            $item_inv->date = $item_inv->date?->format('Y-m-d\TH:i');
            $item_inv->date_time = $item_inv->date?->format('Y-m-d H:i');
            $item_inv->datetime_format = $item_inv->date?->format('d/m/Y h:i A');
            $item_inv->group_items = $items;
            $item_inventories[] = $item_inv;
        }

        $params = [
            'items' => $items,
            'direction' => $direction,
            'item_inventories' => $item_inventories, // =><=

            'end_date' => $end_date->format('d/m/Y'),
            'start_date' => $start_date->format('d/m/Y'),

            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ItemInventory/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function out(Request $request)
    {
        $end_date = now()->parse($request->end_date ?? now())->endOfDay();
        $start_date = now()->parse($request->start_date ?? now()->subDays(2))->startOfDay();
        $direction = 'out';

        $items = CacheItem::get();
        $item_inventories = CacheItemInventory::get()->where('direction', $direction)
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        $params = [
            'items' => $items,
            'direction' => $direction,
            'item_inventories' => $item_inventories,

            'end_date' => $end_date->format('d/m/Y'),
            'start_date' => $start_date->format('d/m/Y'),

            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ItemInventory/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function stock()
    {
        $params = [
            'items' => UseStock::itemsStock(),
        ];

        return Inertia::render('Operation/ItemInventory/Stock', $params);
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
            'date' => now()->format('Y-m-d\TH:i'),
            'units' => (new Item)->units,
            'items' => $items,
        ];

        return Inertia::render('Operation/ItemInventory/Create', $params);
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
            'inventory_date' => ['required', 'date'],
            'group_items.*.item_id' => ['required', 'exists:items,id'],
            'group_items.*.quantity' => ['required', 'numeric'],
            'group_items.*.unit' => ['required'],
            'group_items.*.rate' => ['nullable', 'numeric'],
            'group_items.*.total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();

        $item_inventory = new ItemInventory;
        $item_inventory->date = now()->parse($request->inventory_date);
        $item_inventory->serial = Formula::nextItemInventoryNumber();
        $item_inventory->total = $request->total;
        $item_inventory->branch_id = UseBranch::id();
        $item_inventory->save();

        foreach ($request->group_items ?? [] as $group_item) {
            if ($group_item['quantity'] > 0) {
                $item = Item::find($group_item['item_id']);
                if ($item) {
                    ItemInventoryItem::create([
                        'item_id' => $group_item['item_id'],
                        'quantity' => $group_item['quantity'],
                        // 'unit'              => $group_item['unit'],
                        'rate' => $group_item['rate'] ?? 0,
                        'total' => $group_item['total'],
                        'item_inventory_id' => $item_inventory->id,
                    ]);

                    // $item->updateAvgRate();
                }
            }
        }

        DB::commit();
        CacheItemInventory::forget();
        CacheItemInventoryItem::forget();

        return redirect()->route('item_inventory.document', $item_inventory->id)->with('success', __('Item Inventory added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(ItemInventory $item_inventory)
    {
        $params = [
            'item_inventory' => $item_inventory,
        ];

        return Inertia::render('Operation/ItemInventory/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(ItemInventory $item_inventory)
    {
        // add child
        $item_inventory->items;
        $items = CacheItem::get();
        $params = [
            'units' => (new Item)->units,
            'items' => count($items) ? ResourcesItem::collection($items) : [],
            'item_inventory' => $item_inventory,
        ];

        return Inertia::render('Operation/ItemInventory/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, ItemInventory $item_inventory)
    {
        $request->validate([
            'inventory_date' => ['required', 'date'],
            'group_items.*.item_id' => ['required', 'exists:items,id'],
            'group_items.*.quantity' => ['required', 'numeric'],
            'group_items.*.unit' => ['required'],
            'group_items.*.rate' => ['nullable', 'numeric'],
            'group_items.*.total' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();
        $item_inventory->date = now()->parse($request->inventory_date);
        $item_inventory->total = $request->total;
        $item_inventory->save();

        $previous_items = $item_inventory->items->pluck('id')->toArray();
        foreach ($request->group_items ?? [] as $item) {
            if ($item['quantity'] > 0) {
                $item_id = array_key_exists('id', $item) ? $item['id'] : null;
                if (($key = array_search($item_id, $previous_items)) !== false) {
                    unset($previous_items[$key]);
                }

                ItemInventoryItem::updateOrCreate([
                    'id' => $item_id,
                    'item_inventory_id' => $item_inventory->id,
                ], [
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    // 'unit'              => $item['unit'],
                    'rate' => $item['rate'] ?? 0,
                    'total' => $item['total'],
                ]);
            }
        }
        ItemInventoryItem::where('item_inventory_id', $item_inventory->id)->whereIn('id', $previous_items)->delete();

        DB::commit();
        CacheItemInventory::forget();
        CacheItemInventoryItem::forget();

        return back()->with('success', __('Item Inventory modified successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function document(ItemInventory $item_inventory)
    {
        $item_inventory->items;
        $params = [
            'item_inventory' => $item_inventory,
            'documents' => $item_inventory->documents,
        ];

        return Inertia::render('Operation/ItemInventory/Document', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function document_update(Request $request, ItemInventory $item_inventory)
    {
        $request->validate([
            'document_files' => ['required', ''],
        ]);

        DB::beginTransaction();

        foreach ($request->document_files as $key => $file) {
            if ($file->isValid()) {
                $folder_path = date('Y/F/') . $item_inventory->id;
                Storage::makeDirectory($folder_path);

                $file_name = $file->getClientOriginalName();
                $full_path = $folder_path . '/' . $file_name;

                $document_path = $file->storeAs('documents', $full_path, 'public');

                $document = new Document(['path' => $document_path]);
                $item_inventory->documents()->save($document);
            }
        }
        DB::commit();
        CacheDocument::forget();

        return back()->with('success', __('Item modified successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(ItemInventory $item_inventory)
    {
        DB::beginTransaction();
        foreach ($item_inventory->items as $item) {
            $item->delete();
        }
        $item_inventory->delete();
        DB::commit();
        CacheItemInventory::forget();

        return redirect()->route('item_inventory.in')->with('success', __('Item Inventory removed successfully!'));
    }
}
