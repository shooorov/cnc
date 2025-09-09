<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheProductInventory;
use App\Http\Controllers\Controller;
use App\Models\ProductInventory;
use App\UseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductInventoryController extends Controller
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
    public function in(Request $request)
    {
        $end_date = now()->parse($request->end_date ?? now());
        $start_date = now()->parse($request->start_date ?? now()->subDays(2));
        $direction = 'in';

        $product_inventories = CacheProductInventory::get()->where('direction', $direction)
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        $params = [
            'direction' => $direction,
            'product_inventories' => $product_inventories,
            'end_date' => $end_date->format('d/m/Y'),
            'start_date' => $start_date->format('d/m/Y'),

            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ProductInventory/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function out(Request $request)
    {
        $end_date = now()->parse($request->end_date ?? now());
        $start_date = now()->parse($request->start_date ?? now()->subDays(2));
        $direction = 'out';

        $product_inventories = CacheProductInventory::get()->where('direction', $direction)
            ->where('date', '>=', $start_date)
            ->where('date', '<=', $end_date);

        $params = [
            'direction' => $direction,
            'product_inventories' => $product_inventories,

            'end_date' => $end_date->format('d/m/Y'),
            'start_date' => $start_date->format('d/m/Y'),

            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/ProductInventory/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function stock()
    {
        $params = [
            'products' => UseStock::productsStock(),
        ];

        return Inertia::render('Operation/ProductInventory/Stock', $params);
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(ProductInventory $product_inventory)
    {
        $product_inventory->products;
        $params = [
            'product_inventory' => $product_inventory,
        ];

        return Inertia::render('Operation/ProductInventory/Show', $params);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(ProductInventory $product_inventory)
    {
        $direction = $product_inventory->direction;
        DB::beginTransaction();

        foreach ($product_inventory->products as $product) {
            foreach ($product->items as $item) {
                $item->delete();
            }
            $product->delete();
        }
        $product_inventory->item_inventory->delete();

        $product_inventory->delete();
        DB::commit();

        return redirect()->route($direction == 'in' ? 'product_inventory.in' : 'product_inventory_out')->with('success', __('Product Inventory removed successfully!'));
    }

    public function print(ProductInventory $product_inventory)
    {
        $product_inventory->products;
        $params = [
            'product_inventory' => $product_inventory,
        ];

        return Inertia::render('Operation/ProductInventory/Receipt', $params);
    }
}
