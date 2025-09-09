<?php

namespace App;

use Illuminate\Support\Facades\DB;

class CustomProductInventory
{
    public static function stockIn()
    {
        $branch_id = UseBranch::id();

        return DB::table('product_inventory_products')
            ->join('product_inventories', 'product_inventories.id', '=', 'product_inventory_products.product_inventory_id')
            ->join('branches', 'branches.id', '=', 'product_inventories.branch_id')
            ->join('products', 'products.id', '=', 'product_inventory_products.product_id')
            ->select(
                'products.*',
                'branch_id',
                DB::raw('SUM(product_inventory_products.quantity) as quantity_in'),
            )
            ->where('product_inventories.direction', 'in')
            ->whereNull('product_inventories.deleted_at')
            ->whereNull('products.deleted_at')
            ->when($branch_id, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->groupBy(
                'products.id',
                'products.code',
                'products.name',
                'products.english_name',
                'products.rate',
                'products.image',
                'products.discount',
                'products.production_cost',
                'products.vat_applicable',
                'products.margin_amount',
                'products.margin_percent',
                'products.description',
                'products.product_category_id',
                'products.created_at',
                'products.updated_at',
                'products.deleted_at',
                'branch_id',
            )
            ->orderBy('products.name')
            ->get();
    }

    public static function stockOut()
    {
        $branch_id = UseBranch::id();

        return DB::table('product_inventory_products')
            ->join('product_inventories', 'product_inventories.id', '=', 'product_inventory_products.product_inventory_id')
            ->join('branches', 'branches.id', '=', 'product_inventories.branch_id')
            ->join('products', 'products.id', '=', 'product_inventory_products.product_id')
            ->select(
                'products.*',
                'branch_id',
                DB::raw('SUM(product_inventory_products.quantity) as quantity_in'),
            )
            ->where('product_inventories.direction', 'out')
            ->whereNull('product_inventories.deleted_at')
            ->whereNull('products.deleted_at')
            ->when($branch_id, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->groupBy(
                'products.id',
                'products.code',
                'products.name',
                'products.english_name',
                'products.rate',
                'products.image',
                'products.discount',
                'products.production_cost',
                'products.vat_applicable',
                'products.margin_amount',
                'products.margin_percent',
                'products.description',
                'products.product_category_id',
                'products.created_at',
                'products.updated_at',
                'products.deleted_at',
                'branch_id',
            )
            ->orderBy('products.name')
            ->get();
    }

    public static function available()
    {
        $stockOut = self::stockOut();
        $stockIn = self::stockIn();

        return $stockIn->map(function ($item) use ($stockOut) {
            $res = $stockOut->first(fn ($outItem) => $outItem->id == $item->id);
            $item->quantity_out = $res->quantity_out ?? 0;
            $item->remaining = intval($item->quantity_in) - intval($item->quantity_out) - intval($item->adjust_qty ?? 0);
            $item->old_remaining = $item->remaining;

            return $item;
        })->filter(fn ($item) => $item->remaining > 0);
    }
}
